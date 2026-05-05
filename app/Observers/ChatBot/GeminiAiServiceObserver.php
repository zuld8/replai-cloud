<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\FineTunnel;
use App\Models\InternalSetting;
use App\Services\GoogleUserSheetService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiAiServiceObserver
{
    protected $geminiUrl;
    protected $googleSheetService;

    public function __construct()
    {
        $this->googleSheetService = new GoogleUserSheetService();
        $this->geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    }

    /**
     * Detect intent using Gemini AI
     */
    public function detectIntent(
        FineTunnel $fineTunnel,
        string $geminiKey,
        string $message = '',
        $conversations = null,
        string $modeAi = 'standard',
        $media = null
    ) {
        $description = $fineTunnel->description . PHP_EOL . PHP_EOL;
        $usingMedia = $media !== null;
        
        // Build training from Google Sheets
        $dataFromSheets = $this->getGoogleSheetsData($fineTunnel);
        
        if (count($dataFromSheets) > 0) {
            $description .= "Berikut data dari google sheet sebagai tambahan informasi: " . 
                           $this->formatAllDataForGPT($dataFromSheets);
        }

        // Build conversation history
        $conversationHistory = $this->buildConversationHistory($conversations);

        // Build prompt with intent detection instruction
        $systemPrompt = $description . $this->getIntentDetectionPrompt();

        // Select model based on mode and media
        $model = $this->selectModel($modeAi, $usingMedia);

        // Build contents for Gemini
        $contents = $this->buildContents($systemPrompt, $conversationHistory, $message, $media);

        // Generation config for structured output
        $generationConfig = [
            'temperature' => 0,
            'topK' => 1,
            'topP' => 1,
            'maxOutputTokens' => 2048,
            'responseMimeType' => 'application/json',
            'responseSchema' => $this->getIntentSchema()
        ];

        try {
            $response = Http::retry(3, 100)
                ->timeout(60)
                ->post($this->geminiUrl . $model . ":generateContent?key={$geminiKey}", [
                    'contents' => $contents,
                    'generationConfig' => $generationConfig,
                ]); 

            return $response;
        } catch (\Exception $e) { 
            throw $e;
        }
    }

    /**
     * Get answer for question using Gemini
     */
    public function getAnswer(
        FineTunnel $fineTunnel,
        string $geminiKey,
        string $message = '',
        $conversations = null,
        string $modeAi = 'standard',
        $media = null
    ) {
        $description = $fineTunnel->description . PHP_EOL . PHP_EOL;
        $usingMedia = $media !== null;

        // Build training from Google Sheets
        $dataFromSheets = $this->getGoogleSheetsData($fineTunnel);
        
        if (count($dataFromSheets) > 0) {
            $description .= "Berikut data dari google sheet sebagai tambahan informasi: " . 
                           $this->formatAllDataForGPT($dataFromSheets);
        }

        // Add link formatting instruction
        $description .= " Jika kamu ingin mengirim link, kirim HANYA teks link-nya saja, misalnya: https://whatsmail.org. Jangan tambahkan karakter seperti [], {}, (), <>, atau markdown apapun di sekitarnya.";

        // Build conversation history
        $conversationHistory = $this->buildConversationHistory($conversations);

        // Select model
        $model = $this->selectModel($modeAi, $usingMedia);

        // Build contents
        $contents = $this->buildContents($description, $conversationHistory, $message, $media);

        // Generation config
        $generationConfig = [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ];

        try {
            $response = Http::retry(3, 100)
                ->timeout(60)
                ->post($this->geminiUrl . $model . ":generateContent?key={$geminiKey}", [
                    'contents' => $contents,
                    'generationConfig' => $generationConfig,
                ]);
 

            return $response;
        } catch (\Exception $e) {
            Log::error('Gemini Answer Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check audio data - fallback to OpenAI Whisper or Google Speech-to-Text
     * Note: Gemini doesn't support audio transcription yet, so we use alternatives
     */
    public function checkAudioData($url, $geminiKey)
    {
        // For now, return false and let OpenAI handle it
        // Or implement Google Speech-to-Text API here
        return [
            'status' => false,
            'message' => 'Audio transcription not supported for Gemini. Please use OpenAI.'
        ];
    }

    /**
     * Select appropriate Gemini model
     */
    private function selectModel(string $modeAi, bool $usingMedia): string
    {
        if ($modeAi === 'advanced') {
            return $usingMedia ? 'gemini-1.5-pro-latest' : 'gemini-1.5-pro-latest';
        }
        
        // Standard mode
        return $usingMedia ? 'gemini-2.5-flash' : 'gemini-2.5-flash';
    }

    /**
     * Build contents array for Gemini API
     */
    private function buildContents(
        string $systemPrompt, 
        array $conversationHistory, 
        string $message, 
        $media
    ): array {
        $contents = [];

        // Add system prompt as first user message
        if (!empty($systemPrompt)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ];
            
            // Add dummy model response to establish context
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Understood. I will follow the instructions and respond accordingly.']]
            ];
        }

        // Add conversation history
        if (!empty($conversationHistory)) {
            $contents = array_merge($contents, $conversationHistory);
        }

        // Add current message with optional media
        if (!empty($message) || $media !== null) {
            $parts = [];
            
            // Add text if available
            if (!empty($message)) {
                $parts[] = ['text' => $message];
            }

            // Add image if available
            if ($media !== null) {
                $imageData = $this->prepareImageData($media);
                if ($imageData) {
                    $parts[] = [
                        'inlineData' => [
                            'mimeType' => $imageData['mimeType'],
                            'data' => $imageData['data']
                        ]
                    ];
                }
            }

            if (!empty($parts)) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => $parts
                ];
            }
        }

        return $contents;
    }

    /**
     * Build conversation history in Gemini format
     */
    private function buildConversationHistory($conversations): array
    {
        if (empty($conversations)) {
            return [];
        }

        $history = [];

        foreach ($conversations as $conversation) {
            if (empty($conversation->message)) {
                continue;
            }

            $role = $conversation->from === 'user' ? 'user' : 'model';
            
            $history[] = [
                'role' => $role,
                'parts' => [['text' => $conversation->message]]
            ];
        }

        return $history;
    }

    /**
     * Prepare image data for Gemini
     */
    private function prepareImageData($mediaPath): ?array
    {
        try {
            // Remove 'asset()' wrapper if exists and get actual path
            $publicPath = parse_url($mediaPath, PHP_URL_PATH);
            $localPath = public_path($publicPath);

            if (!file_exists($localPath)) { 
                return null;
            }

            $imageContent = file_get_contents($localPath);
            $mimeType = mime_content_type($localPath);

            // Validate image type
            if (!str_starts_with($mimeType, 'image/')) { 
                return null;
            }

            return [
                'mimeType' => $mimeType,
                'data' => base64_encode($imageContent)
            ];
        } catch (\Exception $e) { 
            return null;
        }
    }

    /**
     * Get intent detection prompt
     */
    private function getIntentDetectionPrompt(): string
    {
        return prompt_detect_intent(); // Use existing helper
    }

    /**
     * Get intent detection schema for structured output
     */
    private function getIntentSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'intent' => [
                    'type' => 'string',
                    'enum' => ['media', 'check_shipping', 'question'],
                    'description' => 'The detected intent of the user message'
                ],
                'medias' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'format' => 'uri'
                    ],
                    'description' => 'Array of media URLs to send (for media intent)'
                ],
                'message' => [
                    'type' => 'string',
                    'description' => 'Response message or caption for the media'
                ],
                'address' => [
                    'type' => 'string',
                    'description' => 'Destination address for shipping (check_shipping intent)'
                ],
                'pos_code' => [
                    'type' => 'string',
                    'description' => 'Postal code for shipping calculation (check_shipping intent)'
                ],
                'quantity' => [
                    'type' => 'integer',
                    'description' => 'Quantity of items for shipping (default: 1)'
                ]
            ],
            'required' => ['intent']
        ];
    }

    /**
     * Get Google Sheets data with caching
     */
    private function getGoogleSheetsData(FineTunnel $fineTunnel): array
    {
        $dataFromSheets = [];
        
        foreach ($fineTunnel->gsheets as $sheet) {
            $cacheKey = "gsheet:gemini:{$sheet->id}";
            
            $sheetData = Cache::remember($cacheKey, 1800, function() use ($sheet) {
                return $this->googleSheetService->getAllDataForGPT($sheet->url);
            });
            
            $dataFromSheets = array_merge($dataFromSheets, $sheetData);
        }
        
        return $dataFromSheets;
    }

    /**
     * Format data for GPT/Gemini
     */
    private function formatAllDataForGPT(array $data): string
    {
        $formatted = [];

        foreach ($data as $index => $item) {
            $itemText = [];
            foreach ($item as $key => $value) {
                if (!empty(trim($value))) {
                    $itemText[] = "{$key}: {$value}";
                }
            }
            $formatted[] = ($index + 1) . ". " . implode(", ", $itemText);
        }

        return "\n" . implode("\n", $formatted);
    }

    /**
     * Calculate credit usage for Gemini
     */
    public function calculateCompletions(string $modelAi, int $inputTokens, int $outputTokens): int
    {
        // Gemini pricing is different from OpenAI
        // Adjust based on your pricing model
        
        $settingConfiguration = InternalSetting::first([
            'credit_token_basic', 
            'credit_token_advance'
        ]);

        $totalTokens = $inputTokens + $outputTokens;
        
        if ($modelAi === 'standard') {
            $tokensPerCredit = $settingConfiguration->credit_token_basic ?? 250;
            return ceil($totalTokens / $tokensPerCredit);
        }
        
        if ($modelAi === 'advanced') {
            $tokensPerCredit = $settingConfiguration->credit_token_advance ?? 100;
            return ceil($totalTokens / $tokensPerCredit);
        }

        return 0;
    }

    /**
     * Parse Gemini response to extract tokens
     */
    public function parseTokenUsage($response): array
    {
        try {
            $body = is_string($response) ? json_decode($response, true) : $response;
            
            $inputTokens = $body['usageMetadata']['promptTokenCount'] ?? 0;
            $outputTokens = $body['usageMetadata']['candidatesTokenCount'] ?? 0;
            $totalTokens = $body['usageMetadata']['totalTokenCount'] ?? ($inputTokens + $outputTokens);

            return [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $totalTokens
            ];
        } catch (\Exception $e) { 
            return [
                'input_tokens' => 0,
                'output_tokens' => 0,
                'total_tokens' => 0
            ];
        }
    }

    /**
     * Extract text from Gemini response
     */
    public function extractTextFromResponse($response): ?string
    {
        try {
            $body = is_string($response) ? json_decode($response, true) : $response;
            
            if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
                return $body['candidates'][0]['content']['parts'][0]['text'];
            }

            return null;
        } catch (\Exception $e) { 
            return null;
        }
    }
}