<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Crm\MessagestResource;
use App\Models\Admin\License;
use App\Models\ChatBot\ChatBot;
use App\Models\Courier\CourierFineTunnel;
use App\Models\InternalSetting;
use App\Models\Master\Label;
use App\Models\Meta\InstagramAccount;
use App\Models\Setting;
use App\Observers\ChatBot\BiteshipServiceObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\HistoryChatObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\ChatBot\RajaOngkirServiceObserver;
use App\Observers\Store\StoreObserver;
use App\Services\Platform\InstagramService;
use App\Supports\MimeTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InstagramController extends Controller
{

    protected $historyChatObserver;
    protected $storeObserver;
    protected $openAiServiceObserver;
    protected $geminiAiServiceObserver;
    protected $rajaOngkirObserver;
    protected $biteshipServiceObserver;
    protected $typeMimeData;
    protected $generalSetting;
    protected $instagramService;

    public function __construct(
        HistoryChatObserver $historyChatObserver,
        StoreObserver $storeObserver,
        OpenAiServiceObserver $openAiServiceObserver,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        RajaOngkirServiceObserver $rajaOngkirObserver,
        BiteshipServiceObserver $biteshipServiceObserver,
        InstagramService $instagramService
    ) {
        $this->historyChatObserver = $historyChatObserver;
        $this->storeObserver = $storeObserver;
        $this->openAiServiceObserver = $openAiServiceObserver;
        $this->geminiAiServiceObserver = $geminiAiServiceObserver;
        $this->rajaOngkirObserver = $rajaOngkirObserver;
        $this->biteshipServiceObserver = $biteshipServiceObserver;
        $this->instagramService = $instagramService;
        $this->typeMimeData = MimeTypes::TYPE_MAP;
        $this->generalSetting = Setting::where('merchant_id', null)->first(['open_ai_key', 'ai_option', 'cek_ongkir_option_api', 'cek_ongkir_api', 'ongkir_method']);
    }

    /**
     * Handle webhook requests (GET for verification, POST for events)
     */
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request);
        }

        if ($request->isMethod('post')) {
            return $this->processWebhook($request);
        }

        return response('Method not allowed', 405);
    }

    /**
     * Verify webhook - Handle GET request from Meta
     */
    private function verifyWebhook(Request $request)
    {
        $mode = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        // Verify the token matches
        $license = License::first(['purchase']);
        $verifyToken = $license->purchase;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }



    /**
     * Process incoming webhook events
     */
    private function processWebhook(Request $request)
    {
        try {
            $data = $request->all();

            if (!isset($data['entry'])) {
                return response()->json(['status' => 'ok']);
            }

            foreach ($data['entry'] as $entry) {
                if (!isset($entry['messaging'])) {
                    continue;
                }

                foreach ($entry['messaging'] as $messaging) {
                    if (isset($messaging['delivery'])) { 
                        continue;
                    }

                    if (isset($messaging['read'])) { 
                        continue;
                    }

                    if (isset($messaging['message']['is_echo']) && $messaging['message']['is_echo'] === true) { 
                        continue;
                    }

                    $this->processMessage($messaging, $entry['id']);
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Instagram Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Process individual message
     */
    private function processMessage(array $messaging, string $pageId)
    {
        try {

            // Lookup by Instagram account ID (entry.id = IGSID)
            $instagramAccount = InstagramAccount::where('instagram_id', $pageId)
                ->where('status', 'active')
                ->first();

            // Fallback: entry.id can also be Facebook Page ID when routed via Page webhook
            if (!$instagramAccount) {
                $instagramAccount = InstagramAccount::where('page_id', $pageId)
                    ->where('status', 'active')
                    ->first();
            }

            if (!$instagramAccount) {
                Log::warning("Instagram account not found for page ID: {$pageId}");
                return false;
            }

            $messageData = $this->parseMessageData($messaging);
            if (!$messageData) {
                return false;
            }

            DB::transaction(function () use ($messageData, $instagramAccount) {
                // Get or create chat history
                $histories = $this->getOrCreateHistory($messageData, $instagramAccount);
                if (!$histories) {
                    return false;
                }

                // Check if chat is blocked
                if ($histories->status === 'block') {
                    return true;
                }

                // Check daily limit
                if ($this->isDailyLimitExceeded($instagramAccount)) {
                    return true;
                }

                // Check for duplicate message
                if ($this->isDuplicateMessage($histories, $messageData['messageId'])) {
                    return true;
                }

                // Parse message content
                $messageContent = $this->parseMessageContent($messageData['rawMessage']);

                // Download media if needed
                $mediaInfo = $this->handleMediaDownload($instagramAccount, $messageContent, $messageData['messageType']);

                // Update chat status
                $this->updateChatStatus($histories);

                // Save user message
                $userMessage = $this->saveUserMessage($histories, $messageData, $messageContent, $mediaInfo);

                // Handle audio transcription
                $this->handleAudioTranscription($messageContent, $mediaInfo);

                // Process labels
                if (!empty($messageContent['message'])) {
                    $this->processLabels($instagramAccount, $histories, $messageContent['message']);
                }

                // Send welcome message if first interaction
                $welcomeMessage = $this->handleWelcomeMessage($instagramAccount, $histories);

                // Send webhook if configured
                $this->sendWebhook($instagramAccount, $messageData, $mediaInfo);

                // Process auto replies
                $replyMessage = $this->processAutoReplies($instagramAccount, $histories, $messageContent);

                // Trigger real-time events
                $this->triggerEmit($replyMessage, $userMessage, $welcomeMessage);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing Instagram message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Parse message data from webhook
     */
    private function parseMessageData(array $messaging): ?array
    {
        if (!isset($messaging['sender']['id']) || !isset($messaging['message'])) {
            return null;
        }

        $message = $messaging['message'];
        $messageType = 'text';

        if (isset($message['attachments'])) {
            $attachment = $message['attachments'][0];
            $messageType = $attachment['type'] ?? 'text';
        }

        return [
            'messageId' => $message['mid'],
            'from' => $messaging['sender']['id'],
            'timestamp' => $messaging['timestamp'] ?? time() * 1000,
            'messageType' => $messageType,
            'rawMessage' => $message
        ];
    }

    /**
     * Get or create chat history
     */
    private function getOrCreateHistory(array $messageData, InstagramAccount $instagramAccount): mixed
    {
        $histories = $this->historyChatObserver->getByNumber(
            'personal',
            $messageData['from'],
            null,
            'instagram',
            null,
            null,
            null,
            $instagramAccount->id
        );

        if (!$histories) {
            // Get sender info from Instagram API
            $senderInfo = $this->getSenderInfo($instagramAccount, $messageData['from']);

            $histories = $this->historyChatObserver->createData(
                null,
                'personal',
                $messageData['from'],
                null,
                $instagramAccount->business->merchant_id ?? null,
                $instagramAccount->business_id,
                $senderInfo['name'] ?? 'Instagram User',
                'instagram',
                null,
                null,
                $senderInfo['profile_pic'] ?? null,
                null,
                $instagramAccount->id
            );
        }

        return $histories;
    }

    /**
     * Get sender information from Instagram API
     */
    private function getSenderInfo(InstagramAccount $instagramAccount, string $senderId): array
    {
        try {
            // ✅ Instagram API endpoint untuk mendapatkan info user
            // Pakai Instagram Account ID sebagai prefix
            $response = Http::withToken($instagramAccount->access_token)
                ->get("https://graph.facebook.com/v22.0/{$senderId}", [
                    'fields' => 'id,name,username,profile_pic'
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'name' => $data['name'] ?? ($data['username'] ?? 'Instagram User'),
                    'profile_pic' => $data['profile_pic'] ?? null
                ];
            }
 
        } catch (\Exception $e) {
            Log::error('Failed to get Instagram sender info: ' . $e->getMessage());
        }

        return ['name' => 'Instagram User', 'profile_pic' => null];
    }

    /**
     * Check if daily limit is exceeded
     */
    private function isDailyLimitExceeded(InstagramAccount $instagramAccount): bool
    {
        return $instagramAccount->daily_limit === 'yes' &&
            $instagramAccount->daily_send >= $instagramAccount->limit_per_day;
    }

    /**
     * Check for duplicate message
     */
    private function isDuplicateMessage($histories, string $messageId): bool
    {
        return $histories->details()
            ->where('messageid', $messageId)
            ->exists();
    }

    /**
     * Parse message content
     */
    private function parseMessageContent(array $rawMessage): array
    {
        $message = '';
        $mediaUrl = null;
        $mimeType = null;
        $messageType = 'text';

        if (isset($rawMessage['text'])) {
            $message = $rawMessage['text'];
        }

        if (isset($rawMessage['attachments'])) {
            $attachment = $rawMessage['attachments'][0];
            $messageType = $attachment['type'] ?? 'file';
            $mediaUrl = $attachment['payload']['url'] ?? null;

            switch ($messageType) {
                case 'image':
                    $mimeType = 'image/jpeg';
                    break;
                case 'video':
                    $mimeType = 'video/mp4';
                    break;
                case 'audio':
                    $mimeType = 'audio/mpeg';
                    break;
                default:
                    $mimeType = 'application/octet-stream';
            }
        }

        return [
            'message' => $message,
            'mediaUrl' => $mediaUrl,
            'mimeType' => $mimeType,
            'messageType' => $messageType
        ];
    }

    /**
     * Handle media download
     */
    private function handleMediaDownload(InstagramAccount $instagramAccount, array $messageContent, string $messageType): array
    {
        $mediaInfo = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        // Check time-based auto reply settings
        if (!$this->checkingTimeAutoReply($instagramAccount)) {
            return $mediaInfo;
        }

        if ($messageType === 'text' || empty($messageContent['mediaUrl'])) {
            return $mediaInfo;
        }

        try {
            // Download media from Instagram URL
            $fileResponse = Http::get($messageContent['mediaUrl']);

            if (!$fileResponse->successful()) {
                throw new \Exception('Failed to download media');
            }

            $fileContent = $fileResponse->body();
            $mimeType = $messageContent['mimeType'] ?? 'application/octet-stream';
            $fileSize = strlen($fileContent);

            // Check storage capacity
            $setting = $instagramAccount->business;
            $storageCheck = $this->checkStorage($setting, $fileSize);

            if (!$storageCheck['available']) { 
                return $mediaInfo;
            }

            // Determine subfolder based on MIME type
            $subFolder = $this->getSubFolderByMimeType($mimeType);
            $uploadPath = "folders/{$setting->id}/{$subFolder}/";
            $this->ensureDirectoryExists($uploadPath);

            // Save file
            $extension = explode('/', $mimeType)[1] ?? 'bin';
            $fileName = uniqid('ig_', true) . '.' . $extension;
            $publicPath = 'uploads/' . $uploadPath . $fileName;
            $fullPath = public_path($publicPath);

            file_put_contents($fullPath, $fileContent);

            // Get real file info
            $mimeType = mime_content_type($fullPath);
            $fileSize = filesize($fullPath);
            $messageType = $this->getMessageTypeFromMime($mimeType);

            $mediaInfo = [
                'status' => true,
                'type' => $mimeType,
                'size' => $fileSize,
                'path' => $publicPath,
                'messageType' => $messageType
            ];
 
        } catch (\Exception $e) {
            Log::error('Instagram media download error: ' . $e->getMessage() . ' - ' . $e->getLine() . ' - ' . $e->getFile());
        }

        return $mediaInfo;
    }

    /**
     * Get message type from MIME type
     */
    private function getMessageTypeFromMime(string $mimeType): string
    {
        foreach ($this->typeMimeData as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $type;
            }
        }
        return 'file';
    }

    /**
     * Update chat status to open
     */
    private function updateChatStatus($histories): void
    {
        if ($histories->status !== 'open') {
            $histories->update(['status' => 'open']);
        }
    }

    /**
     * Save user message to database
     */
    private function saveUserMessage($histories, array $messageData, array $messageContent, array $mediaInfo): mixed
    {
        // Update or create store
        $stores = $this->storeObserver->checkByNumber($histories->from_number, $histories->business_id);
        if (!$stores) {
            $stores = $this->storeObserver->createByHistory($histories);
            $histories->update(['store_id' => $stores->id]);
        }

        // Create message record
        $userMessage = $histories->details()->create([
            'file_path' => $mediaInfo['path'],
            'file_type' => $mediaInfo['type'],
            'file_size' => $mediaInfo['size'],
            'type' => $messageContent['messageType'],
            'history_chat_id' => $histories->id,
            'from' => 'user',
            'message' => $messageContent['message'],
            'remotejid' => $messageData['from'],
            'messageid' => $messageData['messageId']
        ]);

        // Mark follow-ups
        $this->markPreviousFollowUps($histories);

        return $userMessage;
    }

    /**
     * Mark previous messages as follow-up
     */
    private function markPreviousFollowUps($histories): void
    {
        $histories->details()
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->update([
                'is_follow_up' => 'yes',
                'follow_up_id' => null
            ]);
    }

    /**
     * Handle audio transcription - UPDATED WITH GEMINI SUPPORT
     */
    private function handleAudioTranscription(array &$messageContent, array $mediaInfo): void
    {
        if ($messageContent['messageType'] !== 'audio' || !$mediaInfo['status']) {
            return;
        }

        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        if ($ai_option === 'gemini') {
            $transcription = $this->geminiAiServiceObserver->checkAudioData(
                $mediaInfo['path'],
                $this->generalSetting->open_ai_key
            );
        } else {
            $transcription = $this->openAiServiceObserver->checkAudioData(
                $mediaInfo['path'],
                $this->generalSetting->open_ai_key
            );
        }

        if ($transcription['status']) {
            $messageContent['message'] = $transcription['message'];
        }
    }

    /**
     * Process labels based on message content
     */
    private function processLabels(InstagramAccount $instagramAccount, $histories, string $message): void
    {
        if (!$instagramAccount->finetunnel || empty($message)) {
            return;
        }

        $labelIds = explode(',', $instagramAccount->finetunnel->label);
        $matchingLabel = Label::where('business_id', $instagramAccount->business_id)
            ->where('type', 'keyword')
            ->whereIn('id', $labelIds)
            ->whereRaw("? REGEXP REPLACE(tag, ', ', '|')", [$message])
            ->first(['id', 'name']);

        if ($matchingLabel) {
            $this->addLabelToHistory($histories, $matchingLabel);
        }
    }

    /**
     * Add label to chat history
     */
    private function addLabelToHistory($histories, $matchingLabel): void
    {
        $currentLabels = json_decode($histories->label, true) ?? [];
        $alreadyExists = collect($currentLabels)->contains('id', $matchingLabel->id);

        if (!$alreadyExists) {
            $currentLabels[] = [
                'id' => $matchingLabel->id,
                'name' => $matchingLabel->name,
            ];

            $histories->update(['label' => json_encode($currentLabels)]);

            if ($histories->store && $histories->store->label_id == null) {
                $histories->store->update(['label_id' => $matchingLabel->id]);
            }
        }
    }

    /**
     * Handle welcome message for first interaction
     */
    private function handleWelcomeMessage(InstagramAccount $instagramAccount, $histories): mixed
    {
        if (
            !$instagramAccount->finetunnel ||
            !$instagramAccount->finetunnel->welcome_message ||
            $histories->details->count() > 1
        ) {
            return null;
        }

        $welcomeData = $this->prepareWelcomeMessageData($instagramAccount);
        $messageText = str_replace('{name}', $histories->name ?? '', $instagramAccount->finetunnel->welcome_message ?? '');

        $welcomeMessage = $histories->details()->create([
            'message' => $messageText,
            'file_path' => $welcomeData['path'],
            'file_type' => $welcomeData['type'],
            'file_size' => $welcomeData['size'],
            'type' => $welcomeData['messageType'],
            'history_chat_id' => $histories->id,
            'from' => 'device'
        ]);

        $this->sendWelcomeMessageToUser($instagramAccount, $histories, $welcomeMessage);

        return $welcomeMessage;
    }

    /**
     * Prepare welcome message data
     */
    private function prepareWelcomeMessageData(InstagramAccount $instagramAccount): array
    {
        $messageType = 'text';
        $imageData = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        if ($instagramAccount->finetunnel->welcome_image && file_exists($instagramAccount->finetunnel->welcome_image)) {
            $filePath = public_path($instagramAccount->finetunnel->welcome_image);
            $fileType = mime_content_type($filePath);
            $fileSize = filesize($filePath);

            $imageData = [
                'status' => true,
                'type' => $fileType,
                'size' => $fileSize,
                'path' => $instagramAccount->finetunnel->welcome_image
            ];

            $messageType = $this->getMessageTypeFromMime($fileType);
        }

        return [
            'path' => $imageData['path'],
            'type' => $imageData['type'],
            'size' => $imageData['size'],
            'messageType' => $messageType
        ];
    }

    /**
     * Send welcome message to user
     */
    private function sendWelcomeMessageToUser(InstagramAccount $instagramAccount, $histories, $welcomeMessage): void
    {
        $this->sendTextMessageToUser($instagramAccount, $histories, $welcomeMessage->message, $welcomeMessage);
    }

    /**
     * Send webhook if configured
     */
    private function sendWebhook(InstagramAccount $instagramAccount, array $messageData, array $mediaInfo): void
    {
        // Instagram webhook implementation (if needed)
        // Similar to WABA's sendCustomWebHook
    }

    /**
     * Process auto replies
     */
    private function processAutoReplies(InstagramAccount $instagramAccount, $histories, array $messageContent): mixed
    {
        $message = $messageContent['message'];
        $method = $instagramAccount->auto_reply_method;

        if ($method === 'chatbot') {
            if ($this->shouldUseChatbot($instagramAccount, $histories)) {
                return $this->processChatbotReply($instagramAccount, $histories, $message);
            }
            return null;
        }

        if ($method === 'ai') {
            if ($this->shouldUseAI($instagramAccount, $histories)) {
                return $this->processAIReply($instagramAccount, $histories, $messageContent);
            }
            return null;
        }

        if ($method === 'all') {
            if ($this->shouldUseChatbot($instagramAccount, $histories)) {
                $chatbotReply = $this->processChatbotReply($instagramAccount, $histories, $message);

                if ($chatbotReply !== null) {
                    return $chatbotReply;
                }
            }

            if ($this->shouldUseAI($instagramAccount, $histories)) {
                return $this->processAIReply($instagramAccount, $histories, $messageContent);
            }
        }

        return null;
    }

    /**
     * Check if should use chatbot
     */
    private function shouldUseChatbot(InstagramAccount $instagramAccount, $histories): bool
    {
        return in_array($instagramAccount->auto_reply_method, ['chatbot', 'all']) &&
            $histories->status !== 'block';
    }

    /**
     * Check if should use AI
     */
    private function shouldUseAI(InstagramAccount $instagramAccount, $histories): bool
    {
        $settings = $instagramAccount->business;

        return in_array($instagramAccount->auto_reply_method, ['ai', 'all']) &&
            $histories->takeover === 'no' &&
            $histories->status !== 'block' &&
            $settings->is_online === 'yes' &&
            $instagramAccount->finetunnel &&
            $this->hasCredits($instagramAccount);
    }

    /**
     * Process chatbot reply
     */
    private function processChatbotReply(InstagramAccount $instagramAccount, $histories, string $message): mixed
    {
        $reply = $this->autoReplyMessage($instagramAccount, $message, $histories->name, $histories->from_number);

        if (!$reply['status']) {
            return null;
        }

        $followUps = $instagramAccount->finetunnel ? $instagramAccount->finetunnel->follow_ups->count() : 0;

        $replyMessage = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => $reply['message_text'],
        ]);

        $this->sendTextMessageToUser($instagramAccount, $histories, $reply['message_text'], $replyMessage);

        return $replyMessage;
    }

    /**
     * Detect intent using Gemini AI (NEW METHOD)
     */
    private function detectIntentWithGemini(
        $fineTunnel,
        string $message,
        $conversations,
        string $model_ai,
        $image_path = null
    ): array {
        $gemini_key = $this->generalSetting->open_ai_key ?? null;

        if (empty($gemini_key)) {
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }

        try {
            $intent_response = $this->geminiAiServiceObserver->detectIntent(
                $fineTunnel,
                $gemini_key,
                $message,
                $conversations,
                $model_ai,
                $image_path
            );

            if ($intent_response->status() !== 200) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            $response_body = json_decode($intent_response->body(), true);

            // Extract text from Gemini response
            $intent_text = $this->geminiAiServiceObserver->extractTextFromResponse($response_body);

            if (empty($intent_text)) {
                return ['success' => false, 'intent' => null, 'credit_used' => 0];
            }

            // Parse JSON intent
            $intent = json_decode($intent_text);

            // Parse token usage
            $token_usage = $this->geminiAiServiceObserver->parseTokenUsage($response_body);
            $credit_used = $token_usage['total_tokens'];

            return [
                'success' => true,
                'intent' => $intent,
                'credit_used' => $credit_used
            ];
        } catch (\Exception $e) {
            Log::error('Instagram Gemini intent detection error: ' . $e->getMessage());
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }
    }

    /**
     * Detect intent using OpenAI (EXTRACTED METHOD)
     */
    private function detectIntentWithOpenAI(
        $fineTunnel,
        string $message,
        $conversations,
        string $model_ai,
        $image_path = null
    ): array {
        $intent_response = $this->openAiServiceObserver->detectIntent(
            $fineTunnel,
            $this->generalSetting->open_ai_key,
            $message,
            $conversations,
            $model_ai,
            $image_path
        );

        if ($intent_response->status() !== 200) {
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }

        $response_body = json_decode($intent_response->body());

        if (!isset($response_body->choices[0])) {
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }

        $intent = json_decode($response_body->choices[0]->message->content);
        $credit_used = $response_body->usage->total_tokens ?? 0;

        return [
            'success' => true,
            'intent' => $intent,
            'credit_used' => $credit_used
        ];
    }

    /**
     * Detect intent - NEW METHOD WITH GEMINI SUPPORT
     */
    private function detectIntent($fineTunnel, $conversations, string $message, $image_path = null): array
    {
        $model_ai = $fineTunnel->model_ai;
        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        // Switch between OpenAI and Gemini
        if ($ai_option === 'gemini') {
            return $this->detectIntentWithGemini(
                $fineTunnel,
                $message,
                $conversations,
                $model_ai,
                $image_path
            );
        }

        // Default: OpenAI
        return $this->detectIntentWithOpenAI(
            $fineTunnel,
            $message,
            $conversations,
            $model_ai,
            $image_path
        );
    }

    /**
     * Process AI reply - UPDATED WITH GEMINI SUPPORT
     */
    private function processAIReply(InstagramAccount $instagramAccount, $histories, array $messageContent): mixed
    {
        $fineTunnel = $instagramAccount->finetunnel;
        $conversations = $histories->details_desc->take($fineTunnel->history_limit)->sortBy('created_at');
        $imagePath = $messageContent['messageType'] === 'image' ? ($messageContent['path'] ?? null) : null;

        // 🔥 USE NEW detectIntent() method with Gemini support
        $intentData = $this->detectIntent(
            $fineTunnel,
            $conversations,
            $messageContent['message'] ?? '',
            $imagePath
        );

        if (!$intentData['success']) {
            return null;
        }

        $intent = $intentData['intent'];
        $creditUsed = $intentData['credit_used'];

        // Normalize intent object
        if (is_object($intent) && isset($intent->properties) && is_object($intent->properties)) {
            foreach ($intent->properties as $key => $value) {
                $intent->{$key} = $value;
            }
            unset($intent->properties);
        }

        $replyMessage = null;

        switch ($intent->intent) {
            case 'media':
                $replyMessage = $this->handleMediaIntent($intent, $histories, $instagramAccount);
                break;

            case 'check_shipping':
                $replyMessage = $this->handleShippingIntent($intent, $histories, $fineTunnel);
                break;

            case 'question':
                $replyMessage = $this->handleQuestionIntent($intent, $histories, $instagramAccount);
                break;
        }

        // Update credits
        if ($creditUsed > 0) {
            $this->updateCredits($creditUsed, $instagramAccount, $fineTunnel, $replyMessage);
        }

        // Check for transfer conditions
        $this->checkTransferConditions($instagramAccount, $histories, $intent->message ?? '');

        return $replyMessage;
    }


    /**
     * Handle media intent
     */
    private function handleMediaIntent($intent, $histories, InstagramAccount $instagramAccount): mixed
    {
        $mediaUrls = $intent->medias ?? [];
        $replyText = $intent->message ?? '';
        $lastReplyMessage = null;

        foreach ($mediaUrls as $mediaUrl) {
            $mediaInfo = $this->getMediaInfo($mediaUrl);
            $typeMessage = $this->getMessageTypeFromMime($mediaInfo['type'] ?? '');

            if ($typeMessage === 'file') {
                $typeMessage = 'document';
            }

            $replyMessage = $histories->details()->create([
                'file_path' => $mediaUrl,
                'file_type' => $mediaInfo['type'],
                'file_size' => $mediaInfo['size'],
                'history_chat_id' => $histories->id,
                'from' => 'device',
                'is_read' => 'yes',
                'type' => $typeMessage,
                'is_follow_up' => 'yes',
                'message' => $replyText,
                'credit_using' => 0,
            ]);

            $this->sendMediaMessageToUser($instagramAccount, $histories, $replyMessage, $typeMessage);
            $lastReplyMessage = $replyMessage;
        }

        return $lastReplyMessage;
    }

    /**
     * Handle shipping check intent
     */
    private function handleShippingIntent($intent, $histories, $fineTunnel): mixed
    {
        $settings = $histories->business;
        $shippingConfig = $this->getShippingConfig($fineTunnel, $settings);

        if (!$shippingConfig['available']) {
            $replyText = 'Maaf kak 🙏, aku nggak bisa bantu cek ongkirnya langsung nih. Tapi kalau mau, aku bisa kasih cara atau link biar kakak bisa cek sendiri 😊';
        } else {
            $replyText = $this->processShippingCheck($intent, $fineTunnel, $settings);
        }

        $reply = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => 'yes',
            'message' => $replyText,
        ]);

        $this->sendTextMessageToUser($histories->instagram, $histories, $replyText, $reply);

        return $reply;
    }

    /**
     * Handle question intent
     */
    private function handleQuestionIntent($intent, $histories, InstagramAccount $instagramAccount): mixed
    {
        $replyText = $this->cleanText($intent->message);

        $reply = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => 'yes',
            'message' => $replyText
        ]);

        $this->sendTextMessageToUser($instagramAccount, $histories, $replyText, $reply);
        return $reply;
    }

    /**
     * Check if device has credits
     */
    private function hasCredits(InstagramAccount $instagramAccount): bool
    {
        if (!($instagramAccount->business->merchant ?? null)) {
            return true;
        }

        $topupLimit = $instagramAccount->business->package_active_topup->sisa_credit ?? 0;
        $packageCredit = $instagramAccount->business->package_active->sisa_credit ?? 0;

        return $topupLimit > 0 || $packageCredit > 0;
    }

    /**
     * Auto reply message using chatbot
     */
    private function autoReplyMessage(InstagramAccount $instagramAccount, string $message, string $name, string $from): array
    {
        $chatBot = ChatBot::whereRaw("find_in_set('" . $instagramAccount->id . "',select_instagram)")
            ->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])
            ->first();

        if (!$chatBot) {
            return ['status' => false, 'message' => null];
        }

        if ($chatBot->reply_method == 'text') {
            $messageText = str_replace('{name}', $name ?? '', $chatBot->message);

            return [
                'status' => true,
                'message_text' => $messageText,
                'method' => $chatBot->reply_method,
                'message' => ['text' => $messageText]
            ];
        }

        return ['status' => false, 'message' => null];
    }

    /**
     * Send text message to user via Instagram API
     */
    private function sendTextMessageToUser(InstagramAccount $instagramAccount, $histories, string $message, $replyMessage): void
    {
        try {

            $response   = $this->instagramService->sendMessage($instagramAccount, 'text', $histories->from_number, $message, null, null);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyMessage->update(['messageid' => $responseData['message_id'] ?? null]);
            } else { 
            }
        } catch (\Exception $e) {
            Log::error('Instagram send message error: ' . $e->getMessage());
        }
    }

    /**
     * Send media message to user via Instagram API
     */
    private function sendMediaMessageToUser(InstagramAccount $instagramAccount, $histories, $replyMessage, string $typeMessage): void
    {
        try {
            $attachmentType = $typeMessage === 'document' ? 'file' : $typeMessage;
            $response       = $this->instagramService->sendMessage($instagramAccount, 'file', $histories->from_number, '', $replyMessage->file_path, $attachmentType);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyMessage->update(['messageid' => $responseData['message_id'] ?? null]);
            }
        } catch (\Exception $e) {
            Log::error('Instagram send media error: ' . $e->getMessage());
        }
    }

    /**
     * Get shipping configuration
     */
    private function getShippingConfig($fineTunnel, $settings): array
    {
        $hasPackageAccess = ($settings->package_active && $settings->package_active->cek_ongkir === 'yes') ||
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

    /**
     * Process shipping check
     */
    private function processShippingCheck($intent, $fineTunnel, Setting $settings): string
    {
        $posCode = $intent->pos_code ?? null;
        $quantity = $intent->quantity ?? 1;
        $address = $intent->address ?? '';
        $replyMessage = $intent->message ?? '';

        if ($posCode === "null" || $posCode === "" || $posCode == null) {
            return $this->cleanText($replyMessage);
        }

        $apikey = $this->generalSetting->cek_ongkir_option_api === 'sistem'
            ? $this->generalSetting->cek_ongkir_api
            : $settings->cek_ongkir_api ?? '';
        $apiMethod = $this->generalSetting->ongkir_method;

        if ($apiMethod === 'rajaongkir') {
            return $this->processRajaOngkir($fineTunnel, $apikey, $posCode, $replyMessage);
        }

        if ($apiMethod === 'biteship') {
            return $this->processBiteship($fineTunnel, $apikey, $posCode, $quantity, $address, $replyMessage);
        }

        return 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
    }

    /**
     * Process RajaOngkir shipping
     */
    private function processRajaOngkir($fineTunnel, string $apikey, string $posCode, string $replyMessage): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode('|');

        $getOngkir = $this->rajaOngkirObserver->checkOngkir(
            $apikey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices
        );

        if ($getOngkir->status() === 200) {
            $ongkirData = json_decode($getOngkir->body(), true);
            foreach ($ongkirData['data'] as $ongkir) {
                $replyMessage .= "- {$ongkir['name']} - {$ongkir['service']} ({$ongkir['etd']} hari): Rp " .
                    number_format($ongkir['cost'], 0, ',', '.') . "\n";
            }
            return $replyMessage;
        }

        return 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
    }

    /**
     * Process Biteship shipping
     */
    private function processBiteship($fineTunnel, string $apikey, string $posCode, int $quantity, string $address, string $replyMessage): string
    {
        $allowedServices = CourierFineTunnel::where('finetunnel_id', $fineTunnel->id)
            ->groupBy('code')
            ->pluck('code')
            ->implode(',');

        $getOngkir = $this->biteshipServiceObserver->checkOngkir(
            $apikey,
            $fineTunnel->zip_code,
            $posCode,
            (int)$fineTunnel->weight,
            $allowedServices,
            $quantity
        );

        if ($getOngkir->status() === 200) {
            $ongkirData = json_decode($getOngkir->body(), true);

            $origin = $ongkirData['origin']['administrative_division_level_4_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_3_name'] . ', ' .
                $ongkirData['origin']['administrative_division_level_2_name'];

            $reply = "📦 Detail Pengecekan Ongkos Kirim\n";
            $reply .= "Jumlah barang: *{$quantity}*\n";
            $reply .= "🚚 Pengiriman\n";
            $reply .= "Dari: *{$origin}*\n";
            $reply .= "Ke: *{$address}*\n";
            $reply .= "Berikut detail ongkirnya:\n\n";

            foreach ($ongkirData['pricing'] as $ongkir) {
                $duration = $ongkir['duration'];
                $courier = $ongkir['courier_name'];
                $service = $ongkir['courier_service_name'];
                $price = number_format($ongkir['price'], 0, ',', '.');

                $reply .= "- $courier - $service ($duration): Rp $price\n";
            }

            return $reply;
        }

        return 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
    }

    /**
     * Update credits after AI usage
     */
    private function updateCredits(int $creditUsed, InstagramAccount $instagramAccount, $fineTunnel, $replyMessage): void
    {
        if ($creditUsed <= 0 || is_null($instagramAccount->business->merchant ?? null)) {
            return;
        }

        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $totalCreditUsing = $this->calculateCreditUsage($creditUsed, $fineTunnel->model_ai, $setting);

        $this->deductCredits($instagramAccount, $totalCreditUsing);

        if ($replyMessage) {
            $replyMessage->update(['credit_using' => $totalCreditUsing]);
        }
    }

    /**
     * Calculate credit usage - UPDATED WITH GEMINI SUPPORT
     */
    private function calculateCreditUsage(int $creditUsed, string $modelAi, $setting): int
    {
        $ai_option = $this->generalSetting->ai_option ?? 'chatgpt';

        // Gemini has different calculation
        if ($ai_option === 'gemini') {
            return $this->geminiAiServiceObserver->calculateCompletions(
                $modelAi,
                $creditUsed,
                0
            );
        }

        // OpenAI calculation (existing logic)
        $creditPer250Tokens = 1;
        $tokensPerCredit = $modelAi === 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($creditUsed / $tokensPerCredit) * $creditPer250Tokens;
    }

    /**
     * Deduct credits from package
     */
    private function deductCredits(InstagramAccount $instagramAccount, int $totalCreditUsing): void
    {
        $packageCredit = $instagramAccount->business->package_active->sisa_credit ?? 0;

        if ($packageCredit > 0) {
            $instagramAccount->business->package_active->update([
                'using_credit_limit' => $instagramAccount->business->package_active->using_credit_limit + $totalCreditUsing
            ]);
        } else {
            $instagramAccount->business->package_active_topup->update([
                'using_credit_limit' => $instagramAccount->business->package_active_topup->using_credit_limit + $totalCreditUsing
            ]);
        }
    }

    /**
     * Check transfer conditions
     */
    private function checkTransferConditions(InstagramAccount $instagramAccount, $histories, string $message): void
    {
        if ($histories->takeover !== 'no') {
            return;
        }

        $termCondition = $instagramAccount->finetunnel->transfer_condition ?? null;
        if (!$termCondition) {
            return;
        }

        $keywords = array_map('trim', explode(',', $termCondition));
        $term_text = strtolower($message);

        foreach ($keywords as $keyword) {
            if (stripos($term_text, strtolower($keyword)) === false) {
                continue;
            }

            $histories->update(['takeover' => 'yes']);
            break;
        }
    }

    /**
     * Clean text from unwanted characters
     */
    private function cleanText($text)
    {
        // Pisahkan kata sebelum link dalam format: Kata(http://...)
        $text = preg_replace('/([^\(]+)\((https?:\/\/[^\s\)]+)\)/', '$1 $2', $text);

        // Hapus karakter penutup seperti ] yang menempel di akhir URL
        $text = preg_replace('/(https?:\/\/[^\s\]]+)\]/', '$1', $text);

        // Simpan URL sebagai placeholder
        preg_match_all('/https?:\/\/[^\s]+/', $text, $matches);
        $urls = $matches[0];
        foreach ($urls as $index => $url) {
            $text = str_replace($url, "__URL{$index}__", $text);
        }

        // Ubah ** menjadi *
        $text = str_replace('**', '*', $text);

        // Hapus simbol tidak penting: #, !, {}, [], ()
        $text = preg_replace('/[\#\!\{\}\[\]\(\)]/', '', $text);

        // Hapus karakter non-alfanumerik berdiri sendiri (kecuali -)
        $text = preg_replace('/(?<=\s)[^\p{L}\p{N}-](?=\s)/u', '', $text);

        // Bersihkan per baris
        $text = preg_replace_callback('/^.*$/m', function ($lineMatches) {
            $line = $lineMatches[0];

            return preg_replace_callback('/\S+/', function ($matches) use ($line) {
                $word = $matches[0];

                // Pertahankan URL
                if (preg_match('/^__URL\d+__$/', $word)) return $word;

                // Pertahankan - di awal baris (untuk list)
                if (trim($line)[0] === '-' && $word === '-') return $word;

                // Pertahankan @username jika valid
                if (!preg_match('/^@[\p{L}\p{N}_-]+$/u', $word)) {
                    $word = str_replace('@', '', $word);
                }

                // Pertahankan ? dan : hanya jika di akhir kata
                if (strpos($word, '?') !== false && substr($word, -1) !== '?') {
                    $word = str_replace('?', '', $word);
                }
                if (strpos($word, ':') !== false && substr($word, -1) !== ':') {
                    $word = str_replace(':', '', $word);
                }

                // Pertahankan angka diikuti % (misal: 30%, 50 %)
                if (preg_match('/^\d+\s?%$/', $word)) return $word;

                // Pertahankan simbol mata uang diikuti angka (misal: $50, Rp 1000)
                if (preg_match('/^[$£€¥Rp]+\s?\d+([\.,]\d+)*$/', $word)) return $word;

                // Bersihkan karakter asing, izinkan simbol penting dan emoji
                return preg_replace(
                    '/[^\p{L}\p{N}\.\,\;\_\-\?\:\%\/\$\@\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u',
                    '',
                    $word
                );
            }, $line);
        }, $text);

        // Kembalikan URL dari placeholder
        $seen = [];
        foreach ($urls as $index => $url) {
            $placeholder = "__URL{$index}__";
            if (!in_array($url, $seen)) {
                $text = str_replace($placeholder, $url, $text);
                $seen[] = $url;
            } else {
                $text = str_replace($placeholder, '', $text);
            }
        }

        return $text;
    }

    /**
     * Check time-based auto reply settings
     */
    private function checkingTimeAutoReply(InstagramAccount $instagramAccount): bool
    {
        // Check activation days
        if ($instagramAccount->auto_reply_certain_day == 'yes') {
            if ($instagramAccount->days != null) {
                $day = date("D");
                $getCheck = InstagramAccount::where("id", $instagramAccount->id)
                    ->whereRaw("find_in_set('" . $day . "',days)")
                    ->count();

                if ($getCheck == 0) {
                    return false;
                }
            }
        }

        // Check activation time
        if ($instagramAccount->auto_reply_certain_time == 'yes') {
            if ($instagramAccount->start_time != null) {
                if ($instagramAccount->start_time > date("H:i")) {
                    return false;
                }

                if ($instagramAccount->end_time < date("H:i")) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get media info from URL
     */
    private function getMediaInfo($url)
    {
        $pathInfo = pathinfo($url);
        $headers = get_headers($url, 1);

        $size = isset($headers['Content-Length']) ? (int) $headers['Content-Length'] : null;
        $type = isset($headers['Content-Type']) ? $headers['Content-Type'] : null;

        return [
            'type' => $type,
            'size' => $size,
            'path' => $pathInfo['dirname'] . '/' . $pathInfo['basename'],
        ];
    }

    /**
     * Trigger real-time events
     */
    private function triggerEmit($replyMessage, $userMessage, $welcomeMessage): void
    {
 
        $expressUrl = config('services.express.url') . '/trigger-whatsapp';

        if ($welcomeMessage) {
            Http::post($expressUrl, MessagestResource::make($welcomeMessage));
        }

        if ($userMessage) {
            $response = Http::post($expressUrl, MessagestResource::make($userMessage));

            if ($response->successful() && $replyMessage) {
                Http::post($expressUrl, MessagestResource::make($replyMessage));
            }
        } elseif ($replyMessage) {
            Http::post($expressUrl, MessagestResource::make($replyMessage));
        }
    }

    /**
     * Check storage availability
     */
    private function checkStorage($setting, $fileSize = 0): array
    {
        if ($setting->merchant) {
            $totalSize = 0;
            $path = "uploads/folders/{$setting->id}";

            if (Storage::disk('local')->exists($path)) {
                $files = Storage::disk('local')->allFiles($path);
                foreach ($files as $file) {
                    $totalSize += Storage::disk('local')->size($file);
                }
            }

            $usedStorageMB = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            $storageFromSubscribe = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage = $storageFromSubscribe + $storageFromAddons;

            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available' => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage' => $totalStorage,
                'used_storage' => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size' => $fileSizeMB,
                'has_package' => $totalStorage > 0
            ];
        }

        return [
            'available' => true,
            'total_storage' => 9999999,
            'used_storage' => 0,
            'remaining_storage' => 9999,
            'file_size' => 9999,
            'has_package' => 9999
        ];
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists($path): void
    {
        if (!file_exists('uploads/' . $path)) {
            mkdir('uploads/' . $path, 0755, true);
        }
    }

    /**
     * Get subfolder based on MIME type
     */
    private function getSubFolderByMimeType(string $mimeType): string
    {
        $mainType = explode('/', $mimeType)[0];

        switch ($mainType) {
            case 'image':
                return 'ig-images';
            case 'video':
                return 'ig-video';
            case 'audio':
                return 'ig-audio';
            default:
                return 'ig-document';
        }
    }
}
