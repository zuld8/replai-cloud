<?php

namespace App\Services\Platform\Telegram;

use App\Models\ChatBot\ChatBot;
use App\Models\Courier\CourierFineTunnel;
use App\Models\InternalSetting;
use Illuminate\Support\Facades\Log;

class TelegramHandlerService
{
    private $telegramService;
    private $openAiService;
    private $geminiAiService; // ← NEW
    private $rajaOngkirObserver;
    private $biteshipService;
    private $generalSetting;

    public function __construct(
        $telegramService,
        $openAiService,
        $geminiAiService, // ← NEW PARAMETER
        $rajaOngkirObserver,
        $biteshipService,
        $generalSetting
    ) {
        $this->openAiService = $openAiService;
        $this->geminiAiService = $geminiAiService; // ← NEW
        $this->rajaOngkirObserver = $rajaOngkirObserver;
        $this->biteshipService = $biteshipService;
        $this->generalSetting = $generalSetting;
    }

    public function processResponse($histories, TelegramProcessorService $processor, $settings): array
    {
        $responses = [];
        $device = $processor->getDevice();
        $fineTunnel = $device->finetunnel;
        $message = $processor->getMessage();

        // Process welcome message if first interaction
        if ($fineTunnel && $fineTunnel->welcome_message && $histories->details->count() == 1) {
            $welcomeResponse = $this->createWelcomeMessage($fineTunnel, $histories);
            if ($welcomeResponse) {
                $responses = $welcomeResponse;
            }
        }

        // Process AI responses
        if ($this->shouldProcessAiResponse($device, $histories, $settings, $fineTunnel)) {
            $aiResponse = $this->processAiResponse($histories, $processor, $fineTunnel, $settings);
            if ($aiResponse) {
                $responses = $aiResponse;
            }
        }

        // Process chatbot responses
        if ($this->shouldProcessChatbot($device, $histories)) {
            $chatbotResponse = $this->processChatbotResponse($device, $message, $histories);
            if ($chatbotResponse) {
                $responses = $chatbotResponse;
            }
        }

        return $responses;
    }

    private function createWelcomeMessage($fineTunnel, $histories): ?array
    {
        $messageType = 'text';
        $filePath = null;
        $fileType = null;
        $fileSize = null;

        if ($fineTunnel->welcome_image && file_exists(public_path($fineTunnel->welcome_image))) {
            $filePath = $fineTunnel->welcome_image;
            $fileType = mime_content_type(public_path($filePath));
            $fileSize = filesize(public_path($filePath));
            $messageType = $this->determineMessageTypeFromMime($fileType);
        }

        $record = $histories->details()->create([
            'message' => $fineTunnel->welcome_message ?? '',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'type' => $messageType,
            'history_chat_id' => $histories->id,
            'from' => 'device'
        ]);

        return [
            'message' => $fineTunnel->welcome_message ?? '',
            'file_path' => $filePath ? asset($filePath) : null,
            'from'      => 'welcome',
            'record' => $record
        ];
    }

    private function shouldProcessAiResponse($device, $histories, $settings, $fineTunnel): bool
    {
        if (!in_array($device->auto_reply_method, ['ai', 'all'])) {
            return false;
        }

        if ($histories->takeover != 'no' || $histories->status == 'block') {
            return false;
        }

        if ($settings->is_online != 'yes' || !$fineTunnel) {
            return false;
        }

        // Check credit availability
        if ($device->business->merchant ?? null) {
            $topupLimit = $device->business->package_active_topup->sisa_credit ?? 0;
            $packageCredit = $device->business->package_active->sisa_credit ?? 0;
            return $topupLimit > 0 || $packageCredit > 0;
        }

        return true;
    }

    private function shouldProcessChatbot($device, $histories): bool
    {
        return in_array($device->auto_reply_method, ['chatbot', 'all']) &&
            $histories->status != 'block';
    }

    /**
     * Process AI Response - UPDATED WITH GEMINI SUPPORT
     */
    private function processAiResponse($histories, TelegramProcessorService $processor, $fineTunnel, $settings): ?array
    {
        $message = $processor->getMessage();

        // 🔥 Handle audio to text conversion WITH GEMINI SUPPORT
        if ($processor->getMessageType() == 'audio' && $processor->getFilePath()) {
            $message = $this->handleAudioTranscription($processor->getFilePath());
        }

        if (!$message) {
            return null;
        }

        $conversations = $histories->details_desc->take($fineTunnel->history_limit)->sortBy('created_at');
        $imagePath = $processor->getMessageType() == 'image' ? $processor->getFilePath() : null;

        // 🔥 DETECT INTENT WITH GEMINI SUPPORT
        $intentData = $this->detectIntent(
            $fineTunnel,
            $message,
            $conversations,
            $fineTunnel->model_ai,
            $imagePath
        );

        return $this->handleAiIntentResponse($intentData, $histories, $fineTunnel, $settings, $processor);
    }

    /**
     * Detect Intent - WITH GEMINI SUPPORT (NEW METHOD)
     */
    private function detectIntent($fineTunnel, string $message, $conversations, string $model_ai, $imagePath = null): array
    {
        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        // Switch between OpenAI and Gemini
        if ($ai_option === 'gemini') {
            return $this->detectIntentWithGemini(
                $fineTunnel,
                $message,
                $conversations,
                $model_ai,
                $imagePath
            );
        }

        // Default: OpenAI
        return $this->detectIntentWithOpenAI(
            $fineTunnel,
            $message,
            $conversations,
            $model_ai,
            $imagePath
        );
    }

    /**
     * Detect Intent with OpenAI (EXTRACTED METHOD)
     */
    private function detectIntentWithOpenAI($fineTunnel, string $message, $conversations, string $model_ai, $imagePath): array
    {
        try {
            $intentResponse = $this->openAiService->detectIntent(
                $fineTunnel,
                $this->generalSetting->open_ai_key,
                $message,
                $conversations,
                $model_ai,
                $imagePath
            );

            if ($intentResponse->status() !== 200) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            $responseBody = json_decode($intentResponse->body());

            if (!isset($responseBody->choices[0])) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            $intent = json_decode($responseBody->choices[0]->message->content);
            $credit_used = $responseBody->usage->total_tokens ?? 0;

            return [
                'success' => true,
                'intent' => $intent,
                'credit_used' => $credit_used
            ];
        } catch (\Exception $e) {
            Log::error('Telegram OpenAI intent detection error: ' . $e->getMessage());
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }
    }

    /**
     * Detect Intent with Gemini (NEW METHOD)
     */
    private function detectIntentWithGemini($fineTunnel, string $message, $conversations, string $model_ai, $imagePath): array
    {
        $gemini_key = $this->generalSetting->open_ai_key ?? null;

        if (empty($gemini_key)) {
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }

        try {
            $intent_response = $this->geminiAiService->detectIntent(
                $fineTunnel,
                $gemini_key,
                $message,
                $conversations,
                $model_ai,
                $imagePath
            );

            if ($intent_response->status() !== 200) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            $response_body = json_decode($intent_response->body(), true);

            // Extract text from Gemini response
            $intent_text = $this->geminiAiService->extractTextFromResponse($response_body);

            if (empty($intent_text)) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            // Parse JSON intent
            $intent = json_decode($intent_text);

            // Parse token usage
            $token_usage = $this->geminiAiService->parseTokenUsage($response_body);
            $credit_used = $token_usage['total_tokens'];

            return [
                'success' => true,
                'intent' => $intent,
                'credit_used' => $credit_used
            ];
        } catch (\Exception $e) {
            Log::error('Telegram Gemini intent detection error: ' . $e->getMessage());
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }
    }

    /**
     * Handle Audio Transcription - WITH GEMINI SUPPORT (NEW METHOD)
     */
    private function handleAudioTranscription(string $filePath): ?string
    {
        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        try {
            if ($ai_option === 'gemini') {
                $audioText = $this->geminiAiService->checkAudioData(
                    $filePath,
                    $this->generalSetting->open_ai_key
                );
            } else {
                $audioText = $this->openAiService->checkAudioData(
                    $filePath,
                    $this->generalSetting->open_ai_key
                );
            }

            return $audioText['status'] ? $audioText['message'] : null;
        } catch (\Exception $e) {
            Log::error('Telegram audio transcription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle AI Intent Response - UPDATED
     */
    private function handleAiIntentResponse($intentData, $histories, $fineTunnel, $settings, $processor): ?array
    {
        if (!$intentData['success']) {
            return null;
        }

        $intent = $intentData['intent'];
        $creditUsed = $intentData['credit_used'];

        // Normalize intent object
        if (is_object($intent) && isset($intent->properties)) {
            foreach ($intent->properties as $key => $value) {
                $intent->{$key} = $value;
            }
            unset($intent->properties);
        }

        $response = null;

        switch ($intent->intent) {
            case 'media':
                $response = $this->handleMediaIntent($intent, $histories);
                break;
            case 'check_shipping':
                $response = $this->handleShippingIntent($intent, $fineTunnel, $settings, $histories);
                break;
            case 'question':
                $response = $this->handleQuestionIntent($intent, $histories, $fineTunnel);
                break;
        }

        if ($response && $creditUsed > 0) {
            $this->updateCredits($creditUsed, $processor->getDevice(), $fineTunnel, $response['record'] ?? null);
        }

        return $response;
    }

    private function handleMediaIntent($intent, $histories): ?array
    {
        $mediaUrls = $intent->medias ?? [];
        $responses = [];

        foreach ($mediaUrls as $mediaUrl) {
            $mediaInfo = $this->getMediaInfo($mediaUrl);
            $messageType = $this->determineMessageTypeFromMime($mediaInfo['type']);

            $record = $histories->details()->create([
                'file_path' => $mediaUrl,
                'file_type' => $mediaInfo['type'],
                'file_size' => $mediaInfo['size'],
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'type' => $messageType,
                'is_follow_up' => 'yes',
                'credit_using' => 0,
            ]);

            $responses[] = [
                'message' => '',
                'file_path' => $mediaUrl,
                'from'      => 'device',
                'record' => $record
            ];
        }

        return $responses[0] ?? null;
    }

    private function handleShippingIntent($intent, $fineTunnel, $settings, $histories): ?array
    {
        $shippingConfig = $this->getShippingConfig($fineTunnel, $settings);

        if (!$shippingConfig['available']) {
            $message = 'Maaf kak 🙏, aku nggak bisa bantu cek ongkirnya langsung nih. Tapi kalau mau, aku bisa kasih cara atau link biar kakak bisa cek sendiri 😊';
        } else {
            $message = $this->processShippingCheck($intent, $fineTunnel, $settings);
        }

        $record = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' =>  $fineTunnel->follow_ups->count() > 0 ? 'no' : 'yes',
            'message' => $message,
        ]);

        return [
            'message' => $message,
            'from'      => 'device',
            'record' => $record
        ];
    }

    private function handleQuestionIntent($intent, $histories, $fineTunnel): ?array
    {
        $message = $this->cleanText($intent->message);

        $record = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' =>  $fineTunnel->follow_ups->count() > 0 ? 'no' : 'yes',
            'message' => $message
        ]);

        return [
            'message' => $message,
            'from'      => 'device',
            'record' => $record
        ];
    }

    // Keep existing helper methods
    private function getShippingConfig($fineTunnel, $settings): array
    {
        $hasPackageAccess = ($settings->package_active && $settings->package_active->cek_ongkir == 'yes') ||
            is_null($settings->merchant_id);

        $hasRequiredConfig = $fineTunnel->couriers->count() > 0 &&
            !empty($fineTunnel->zip_code) &&
            (int)$fineTunnel->weight > 0;

        return [
            'available' => $hasPackageAccess && $hasRequiredConfig,
            'region' => $fineTunnel->address ?? '',
            'courier_codes' => $fineTunnel->couriers->pluck('code')->unique()->implode(','),
            'weight' => (int)$fineTunnel->weight,
        ];
    }

    private function processShippingCheck($intent, $fineTunnel, $settings): string
    {
        $posCode = $intent->pos_code ?? null;
        $quantity = $intent->quantity ?? 1;
        $address = $intent->address ?? '';
        $message = $intent->message ?? '';

        if ($posCode === "" || $posCode === "null" || $posCode === null) {
            return $this->cleanText($message);
        }

        $apiKey = $this->generalSetting->cek_ongkir_option_api == 'sistem'
            ? $this->generalSetting->cek_ongkir_api
            : ($settings->cek_ongkir_api ?? '');

        $apiMethod = $this->generalSetting->ongkir_method;

        if ($apiMethod == 'rajaongkir') {
            return $this->processRajaOngkir($fineTunnel, $apiKey, $posCode, $message);
        } elseif ($apiMethod == 'biteship') {
            return $this->processBiteship($fineTunnel, $apiKey, $posCode, $quantity, $address, $message);
        }

        return 'Metode pengiriman tidak didukung.';
    }

    private function processRajaOngkir($fineTunnel, $apiKey, $posCode, $message): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode('|');

        $response = $this->rajaOngkirObserver->checkOngkir(
            $apiKey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices
        );

        if ($response->status() == 200) {
            $ongkirData = json_decode($response->body(), true);
            foreach ($ongkirData['data'] as $ongkir) {
                $message .= "- {$ongkir['name']} - {$ongkir['service']} ({$ongkir['etd']} hari): Rp " . number_format($ongkir['cost'], 0, ',', '.') . "\n";
            }
        } else {
            $message = 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        return $message;
    }

    private function processBiteship($fineTunnel, $apiKey, $posCode, $quantity, $address, $message): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode(',');

        $response = $this->biteshipService->checkOngkir(
            $apiKey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices,
            (int)$quantity
        );

        if ($response->status() == 200) {
            $ongkirData = json_decode($response->body(), true);

            $origin = $ongkirData['origin']['administrative_division_level_4_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_3_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_2_name'];

            $message = "📦 Detail Pengecekan Ongkos Kirim\n";
            $message .= "Jumlah barang: *{$quantity}*\n";
            $message .= "🚚 Pengiriman\n";
            $message .= "Dari: *{$origin}*\n";
            $message .= "Ke: *{$address}*\n";
            $message .= "Berikut detail ongkirnya:\n\n";

            foreach ($ongkirData['pricing'] as $ongkir) {
                $duration = $ongkir['duration'];
                $courier = $ongkir['courier_name'];
                $service = $ongkir['courier_service_name'];
                $price = number_format($ongkir['price'], 0, ',', '.');

                $message .= "- $courier - $service ($duration): Rp $price\n";
            }
        } else {
            $message = 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        return $message;
    }

    private function processChatbotResponse($device, $message, $histories): ?array
    {
        $reply = $this->autoReplyMessage($device, $message, $histories);

        if ($reply['status']) {
            return $reply;
        }

        return null;
    }

    private function autoReplyMessage($device, $message, $histories): array
    {
        $responses = [];
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $device->id . "',select_telegram)")->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return ['status' => false, 'message' => null];
        }

        if ($chatBot->reply_method == 'text') {
            $record = $histories->details()->create([
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => 'yes',
                'message' => $chatBot->message
            ]);

            return [
                'status'  => true,
                'record'  => $record,
                'message' => $chatBot->message,
                'from'    => 'device'
            ];
        }

        if ($chatBot->reply_method == 'template') {
            $file = $chatBot->template->image != null ? asset($chatBot->template->image) : '';
            $messageText = $chatBot->template->message ?? '';

            $record = $histories->details()->create([
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => 'yes',
                'message' => $messageText
            ]);

            return [
                'status'    => true,
                'file_path' => $file,
                'message'   => $messageText,
                'record'    => $record,
                'from'      => 'device'
            ];
        }

        if ($chatBot->reply_method == 'image') {
            foreach ($chatBot->details as $detail) {
                $mediaInfo = $this->getMediaInfo($detail->url);
                $messageType = $this->determineMessageTypeFromMime($mediaInfo['type']);

                $record = $histories->details()->create([
                    'file_path' => $detail->url,
                    'file_type' => $mediaInfo['type'],
                    'file_size' => $mediaInfo['size'],
                    'history_chat_id' => $histories->id,
                    'from' => 'device',
                    'is_read' => 'yes',
                    'type' => $messageType,
                    'is_follow_up' => 'yes',
                    'credit_using' => 0,
                ]);

                $responses[] = [
                    'message' => '',
                    'file_path' => $detail->url,
                    'from'      => 'device',
                    'record' => $record
                ];
            }

            return $responses[0] ?? ['status' => false];
        }

        return ['status' => false];
    }

    private function getMediaInfo($url): array
    {
        try {
            $pathInfo = pathinfo($url);
            $headers = get_headers($url, 1);

            $size = isset($headers['Content-Length']) ? (int)$headers['Content-Length'] : null;
            $type = isset($headers['Content-Type']) ? $headers['Content-Type'] : null;

            return [
                'type' => $type,
                'size' => $size,
                'path' => $pathInfo['dirname'] . '/' . $pathInfo['basename'],
            ];
        } catch (\Exception $e) {
            Log::error('Media info error: ' . $e->getMessage());
            return ['type' => null, 'size' => null, 'path' => null];
        }
    }

    private function determineMessageTypeFromMime(string $mimeType): string
    {
        $typeMimeData = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video' => ['video/mp4', 'video/avi', 'video/mov', 'video/webm'],
            'audio' => ['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mpeg'],
            'document' => ['application/pdf', 'application/msword', 'text/plain']
        ];

        foreach ($typeMimeData as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $type;
            }
        }
        return 'text';
    }

    /**
     * Update Credits - WITH GEMINI SUPPORT
     */
    private function updateCredits(int $creditUsed, $device, $fineTunnel, $replyMessage): void
    {
        if ($creditUsed <= 0 || is_null($device->business->merchant ?? null)) {
            return;
        }

        $totalCreditUsing = $this->calculateCreditUsage($creditUsed, $fineTunnel->model_ai);

        $this->deductCredits($device, $totalCreditUsing);

        if ($replyMessage) {
            $replyMessage->update(['credit_using' => $totalCreditUsing]);
        }
    }

    /**
     * Calculate Credit Usage - WITH GEMINI SUPPORT
     */
    private function calculateCreditUsage(int $creditUsed, string $modelAi): int
    {
        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        // Gemini has different calculation
        if ($ai_option === 'gemini') {
            return $this->geminiAiService->calculateCompletions(
                $modelAi,
                $creditUsed,
                0
            );
        }

        // OpenAI calculation (default)
        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        
        $creditPer250Tokens = 1;
        $tokensPerCredit = $modelAi == 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($creditUsed / $tokensPerCredit) * $creditPer250Tokens;
    }

    private function deductCredits($device, int $totalCreditUsing): void
    {
        $packageCredit = $device->business->package_active->sisa_credit ?? 0;

        if ($packageCredit > 0) {
            $device->business->package_active->increment('using_credit_limit', $totalCreditUsing);
        } else {
            $device->business->package_active_topup->increment('using_credit_limit', $totalCreditUsing);
        }
    }

    private function cleanText($text): string
    {
        $text = preg_replace('/([^\(]+)\((https?:\/\/[^\s\)]+)\)/', '$1 $2', $text);
        $text = preg_replace('/(https?:\/\/[^\s\]]+)\]/', '$1', $text);
        $text = str_replace('**', '*', $text);
        $text = preg_replace('/[\#\!\{\}\[\]\(\)]/', '', $text);
        
        return trim($text);
    }
}
