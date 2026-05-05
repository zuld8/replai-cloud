<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveChat\HistoryChatResources;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\HistoryChat;
use App\Models\Courier\CourierFineTunnel;
use App\Models\InternalSetting;
use App\Models\Master\Label;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappKeyAccount;
use App\Observers\ChatBot\BiteshipServiceObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\HistoryChatObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\ChatBot\RajaOngkirServiceObserver;
use App\Observers\Store\StoreObserver;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Observers\WhatsappOfficial\WhatsappTemplateServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Supports\MimeTypes;
use Illuminate\Support\Facades\Log;

class WabaCallbackController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Whatsapp Official Callback Api Controller
    |--------------------------------------------------------------------------
    */

    protected $openAiServiceObserver;
    protected $whatsappServiceObserver;
    protected $geminiAiServiceObserver;
    protected $historyChatObserver;
    protected $storeObserver;
    protected $whatsappTemplateServiceObserver;
    protected $generalSetting;
    protected $typeMimeData;
    protected $rajaOngkirObserver;
    protected $biteshipServiceObserver;
    protected $wabaCallbackService;

    public function __construct(
        OpenAiServiceObserver $openAiServiceObserver,
        WhatsappOfficialServiceObserver $whatsappServiceObserver,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        HistoryChatObserver $historyChatObserver,
        StoreObserver $storeObserver,
        WhatsappTemplateServiceObserver $whatsappTemplateServiceObserver,
        RajaOngkirServiceObserver $rajaOngkirObserver,
        BiteshipServiceObserver $biteshipServiceObserver,
    ) {
        $this->openAiServiceObserver            = $openAiServiceObserver;
        $this->whatsappServiceObserver          = $whatsappServiceObserver;
        $this->geminiAiServiceObserver          = $geminiAiServiceObserver;
        $this->historyChatObserver              = $historyChatObserver;
        $this->storeObserver                    = $storeObserver;
        $this->whatsappTemplateServiceObserver  = $whatsappTemplateServiceObserver;
        $this->rajaOngkirObserver           = $rajaOngkirObserver;
        $this->biteshipServiceObserver      = $biteshipServiceObserver;
        $this->generalSetting                   = Setting::where('merchant_id', null)->first(['open_ai_key', 'ai_option', 'cek_ongkir_option_api', 'cek_ongkir_api', 'ongkir_method']);
        $this->typeMimeData                     = MimeTypes::TYPE_MAP;
    }


    public function checkTokenVerify(Request $request, Setting $settings)
    {

        if ($settings->id == $request->get('hub_verify_token')) {
            return response($request->get('hub_challenge'), 200)->header('Content-Type', 'text/plain');
        }

        return response('Error, invalid token', 403);
    }

    public function callbackMessage(Request $request, Setting $settings)
    {

        try {
            $data = $request->all();

            if (!$this->validateCallbackStructure($data)) {
                return response()->json(['status' => 'ok']);
            }

            $device = $this->getDevice($data, $settings);
            if (!$device) {
                return response()->json(['status' => 'ok']);
            }

            $messageData = $this->parseMessageData($data);
            if (!$messageData) {
                return response()->json(['status' => 'ok']);
            }

            $result = $this->processMessage($messageData, $device, $settings);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('WhatsApp Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Validate callback structure
     */
    private function validateCallbackStructure(array $data): bool
    {
        return isset($data['entry'][0]['changes'][0]['value']['metadata']['display_phone_number']) &&
            isset($data['entry'][0]['changes'][0]['value']['messages'][0]) &&
            isset($data['entry'][0]['changes'][0]['value']['contacts'][0]);
    }

    /**
     * Get device from callback data
     */
    private function getDevice(array $data, Setting $settings): ?WhatsappKeyAccount
    {
        $phoneNumber = $data['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];

        return WhatsappKeyAccount::where('phone', $phoneNumber)
            ->where('business_id', $settings->id)
            ->first();
    }

    /**
     * Parse message data from callback
     */
    private function parseMessageData(array $data): ?array
    {
        $detailCallback = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $detailContact = $data['entry'][0]['changes'][0]['value']['contacts'][0];

        return [
            'messageId' => $detailCallback['id'],
            'from' => $detailContact['wa_id'],
            'fromName' => $detailContact['profile']['name'],
            'messageType' => $detailCallback['type'] ?? 'text',
            'rawMessage' => $detailCallback,
            'contact' => $detailContact
        ];
    }


    /**
     * Process the incoming message
     */
    private function processMessage(array $messageData, WhatsappKeyAccount $device, Setting $settings): bool
    {


        try {

            DB::transaction(function () use ($messageData, $device, $settings) {
                // Get or create chat history
                $histories = $this->getOrCreateHistory($messageData, $device);
                if (!$histories) {
                    return false;
                }

                // Check if chat is blocked
                if ($histories->status === 'block') {
                    return true;
                }

                // Check daily limit
                if ($this->isDailyLimitExceeded($device)) {
                    return true;
                }

                // Check for duplicate message
                if ($this->isDuplicateMessage($histories, $messageData['messageId'])) {
                    return true;
                }

                // Parse message content
                $messageContent = $this->parseMessageContent($messageData['rawMessage']);

                // Download media if needed
                $mediaInfo = $this->handleMediaDownload($device, $messageContent, $messageData['messageType']);

                // Update chat status
                $this->updateChatStatus($histories);

                // Save user message
                $userMessage = $this->saveUserMessage($histories, $messageData, $messageContent, $mediaInfo);

                // Handle audio transcription
                $this->handleAudioTranscription($messageContent, $mediaInfo);

                // Process labels
                if (!empty($messageContent['message'])) {
                    $this->processLabels($device, $settings, $histories, $messageContent['message']);
                }


                // Send welcome message if first interaction
                $welcomeMessage = $this->handleWelcomeMessage($device, $histories);

                // Send webhook if configured
                $this->sendWebhook($device, $messageData, $mediaInfo);

                // Process auto replies
                $replyMessage = $this->processAutoReplies($device, $settings, $histories, $messageContent);

                // Trigger real-time events
                $this->triggerEmit($replyMessage, $userMessage, $welcomeMessage);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing message: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Get or create chat history
     */
    private function getOrCreateHistory(array $messageData, WhatsappKeyAccount $device): mixed
    {
        $histories = $this->historyChatObserver->getByNumber(
            'personal',
            $messageData['from'],
            null,
            null,
            null,
            $device->id
        );

        if (!$histories) {
            $histories = $this->historyChatObserver->createData(
                null,
                'personal',
                $messageData['from'],
                null,
                $device->merchant_id,
                $device->business_id,
                $messageData['fromName'],
                'waba',
                null,
                $device->id
            );
        }

        return $histories;
    }

    /**
     * Check if daily limit is exceeded
     */
    private function isDailyLimitExceeded(WhatsappKeyAccount $device): bool
    {
        return $device->daily_limit === 'yes' &&
            $device->daily_send >= $device->limit_per_day;
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
     * Parse message content based on type
     */
    private function parseMessageContent(array $rawMessage): array
    {
        $messageType = $rawMessage['type'] ?? 'text';
        $message = '';
        $mediaId = null;
        $mimeType = null;

        switch ($messageType) {
            case 'text':
                $message = $rawMessage['text']['body'] ?? '';
                break;

            case 'image':
                $mediaId = $rawMessage['image']['id'] ?? null;
                $mimeType = $rawMessage['image']['mime_type'] ?? null;
                $message = $rawMessage['image']['caption'] ?? '';
                break;

            case 'video':
                $mediaId = $rawMessage['video']['id'] ?? null;
                $mimeType = $rawMessage['video']['mime_type'] ?? null;
                $message = $rawMessage['video']['caption'] ?? '';
                break;

            case 'audio':
                $mediaId = $rawMessage['audio']['id'] ?? null;
                $mimeType = $rawMessage['audio']['mime_type'] ?? null;
                $message = '';
                break;

            case 'document':
                $mediaId = $rawMessage['document']['id'] ?? null;
                $mimeType = $rawMessage['document']['mime_type'] ?? null;
                $message = $rawMessage['document']['filename'] ?? 'dokumen.pdf';
                break;
        }

        return [
            'message' => $message,
            'mediaId' => $mediaId,
            'mimeType' => $mimeType,
            'messageType' => $messageType
        ];
    }

    /**
     * Handle media download
     */
    private function handleMediaDownload(WhatsappKeyAccount $device, array $messageContent, string $messageType): array
    {
        $mediaInfo = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        // ✅ Cek jam kerja (jika di luar jam operasional, skip)
        if (!$this->checkingTimeAutoReply($device)) {
            return $mediaInfo;
        }

        // ✅ Pastikan bukan pesan teks & ada mediaId
        if ($messageType !== 'text' && !empty($messageContent['mediaId'])) {
            try {
                // 🔹 1. Download media dari WhatsApp API
                $mediaData = $this->downloadMedia($device, $messageContent['mediaId'], $messageContent['mimeType']);

                if (!$mediaData || empty($mediaData['content'])) {
                    throw new \Exception('Media data empty or invalid');
                }

                // 🔹 2. Hitung ukuran & tipe MIME
                $mimeType = $messageContent['mimeType'] ?? 'application/octet-stream';
                $fileSize = strlen($mediaData['content']);

                // 🔹 3. Cek kapasitas storage business
                $setting = $device->business; // pastikan relasi business ada di model WhatsappKeyAccount
                $storageCheck = $this->checkStorage($setting, $fileSize);

                if (!$storageCheck['available']) {
                    Log::warning("Storage full for business ID {$setting->id}");
                    return $mediaInfo;
                }

                // 🔹 4. Tentukan subfolder berdasarkan tipe MIME
                $subFolder = $this->getSubFolderByMimeType($mimeType);
                $uploadPath = "folders/{$setting->id}/{$subFolder}/";
                $this->ensureDirectoryExists($uploadPath);

                // 🔹 5. Simpan file secara fisik
                $extension = explode('/', $mimeType)[1] ?? 'bin';
                $fileName = uniqid('wa_', true) . '.' . $extension;
                $publicPath = 'uploads/' . $uploadPath . $fileName;
                $fullPath = public_path($publicPath);

                file_put_contents($fullPath, $mediaData['content']);

                // 🔹 6. Dapatkan ukuran real & MIME dari file disimpan
                $mimeType = mime_content_type($fullPath);
                $fileSize = filesize($fullPath);

                // 🔹 7. Update message type berdasar MIME
                $messageType = $this->getMessageTypeFromMime($mimeType);

                // 🔹 8. Return informasi media
                $mediaInfo = [
                    'status' => true,
                    'type'   => $mimeType,
                    'size'   => $fileSize,
                    'path'   => $publicPath,
                    'messageType' => $messageType
                ];

                Log::info("WhatsApp media stored: {$publicPath}");
            } catch (\Exception $e) {
                Log::error('WhatsApp media download error: ' . $e->getMessage());
            }
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
        // Update stores
        $stores = $this->storeObserver->checkByNumber($histories->from_number, $histories->business_id);
        if (!$stores) {
            $store =  $this->storeObserver->createByHistory($histories);
        }

        if (empty($histories->store_id)) {
            $histories->update(['store_id'    => $store->id]);
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
     * Handle audio transcription
     */
    private function handleAudioTranscription(array &$messageContent, array $mediaInfo): void
    {
        if ($messageContent['messageType'] === 'audio' && $mediaInfo['status']) {
            $transcription = $this->openAiServiceObserver->checkAudioData(
                $mediaInfo['path'],
                $this->generalSetting->open_ai_key
            );

            if ($transcription['status']) {
                $messageContent['message'] = $transcription['message'];
            }
        }
    }

    /**
     * Process labels based on message content
     */
    private function processLabels(WhatsappKeyAccount $device, Setting $settings, $histories, string $message): void
    {
        if (!$device->finetunnel || empty($message)) {
            return;
        }

        $labelIds = explode(',', $device->finetunnel->label);
        $matchingLabel = Label::where('business_id', $settings->id)
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

            if ($histories->store) {
                if ($histories->store->label_id == null) {
                    $histories->store->update([
                        'label_id'      => $matchingLabel->id
                    ]);
                }
            }
        }
    }

    /**
     * Handle welcome message for first interaction
     */
    private function handleWelcomeMessage(WhatsappKeyAccount $device, $histories): mixed
    {
        if (
            !$device->finetunnel ||
            !$device->finetunnel->welcome_message ||
            $histories->details->count() > 1
        ) {
            return null;
        }

        $welcomeData = $this->prepareWelcomeMessageData($device);
        $messageText    = str_replace('{name}', $histories->name ?? '', $device->finetunnel->welcome_message ?? '');
        $welcomeMessage = $histories->details()->create([
            'message' => $messageText,
            'file_path' => $welcomeData['path'],
            'file_type' => $welcomeData['type'],
            'file_size' => $welcomeData['size'],
            'type' => $welcomeData['messageType'],
            'history_chat_id' => $histories->id,
            'from' => 'device'
        ]);

        $this->sendWelcomeMessageToUser($device, $histories, $welcomeMessage);

        return $welcomeMessage;
    }

    /**
     * Prepare welcome message data
     */
    private function prepareWelcomeMessageData(WhatsappKeyAccount $device): array
    {
        $messageType = 'text';
        $imageData = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        if (file_exists($device->finetunnel->welcome_image)) {
            $filePath = public_path($device->finetunnel->welcome_image);
            $fileType = mime_content_type($filePath);
            $fileSize = filesize($filePath);

            $imageData = [
                'status' => true,
                'type' => $fileType,
                'size' => $fileSize,
                'path' => $device->finetunnel->welcome_image
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
    private function sendWelcomeMessageToUser(WhatsappKeyAccount $device, $histories, $welcomeMessage): void
    {
        $messageVariable = $this->getMessageVariables($device);
        $sendMessage = $this->whatsappServiceObserver->sendTextMessage(
            $histories->store,
            $device->finetunnel->welcome_message,
            $messageVariable
        );

        if ($sendMessage['status'] === 200) {
            $welcomeMessage->update(['messageid' => $sendMessage['messageid']]);
        }
    }

    /**
     * Send webhook if configured
     */
    private function sendWebhook(WhatsappKeyAccount $device, array $messageData, array $mediaInfo): void
    {
        if (!empty($device->webhook)) {
            $this->sendCustomWebHook(
                $messageData['from'],
                $messageData['message'] ?? '',
                $mediaInfo,
                $messageData['messageType'],
                $device
            );
        }
    }

    /**
     * Process auto replies
     */
    private function processAutoReplies(WhatsappKeyAccount $device, Setting $settings, $histories, array $messageContent): mixed
    {
        $message = $messageContent['message'];

        // Chatbot auto-reply
        if ($this->shouldUseChatbot($device, $histories)) {
            return $this->processChatbotReply($device, $histories, $message);
        }

        // AI auto-reply
        if ($this->shouldUseAI($device, $settings, $histories)) {
            return $this->processAIReply($device, $settings, $histories, $messageContent);
        }

        // Any-chat auto-reply
        if ($this->shouldUseAnyReply($device, $histories)) {
            return $this->processAnyReply($device, $histories);
        }

        return null;
    }

    /**
     * Check if should use chatbot
     */
    private function shouldUseChatbot(WhatsappKeyAccount $device, $histories): bool
    {
        return in_array($device->auto_reply_method, ['chatbot', 'all']) &&
            $histories->status !== 'block';
    }

    /**
     * Check if should use AI
     */
    private function shouldUseAI(WhatsappKeyAccount $device, Setting $settings, $histories): bool
    {
        return in_array($device->auto_reply_method, ['ai', 'all']) &&
            $histories->takeover === 'no' &&
            $histories->status !== 'block' &&
            $settings->is_online === 'yes' &&
            $device->finetunnel &&
            $this->hasCredits($device);
    }

    /**
     * Check if should use any-reply
     */
    private function shouldUseAnyReply(WhatsappKeyAccount $device, $histories): bool
    {
        return $device->reply_any_chat === 'yes' &&
            $histories->details->count() === 0;
    }

    /**
     * Process chatbot reply
     */
    private function processChatbotReply(WhatsappKeyAccount $device, $histories, string $message): mixed
    {
        $reply = $this->autoReplyMessage($device, $message, $histories->name, $histories->from_number, 'personal');

        if (!$reply['status']) {
            return null;
        }

        $followUps = $device->finetunnel ? $device->finetunnel->follow_ups->count() : 0;

        $replyMessage = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => $reply['message_text'],
        ]);

        $this->sendTextMessageToUser($device, $histories, $reply['message_text'], $replyMessage);

        return $replyMessage;
    }

    /**
     * Process AI reply
     */
    private function processAIReply(WhatsappKeyAccount $device, Setting $settings, $histories, array $messageContent): mixed
    {
        $fineTunnel     = $device->finetunnel;
        $conversations  = $histories->details_desc->take($fineTunnel->history_limit)->sortBy('created_at'); 
        $imagePath      = $messageContent['messageType'] === 'image' ? $messageContent['path'] ?? null : null;

        $intentResponse = $this->openAiServiceObserver->detectIntent(
            $fineTunnel,
            $this->generalSetting->open_ai_key,
            $messageContent['message'] ?? '',
            $conversations,
            $fineTunnel->model_ai,
            $imagePath
        );

        $responseBody = json_decode($intentResponse->body());

        if ($intentResponse->status() !== 200) {
            return null;
        }

        return $this->handleAIResponse($responseBody, $device, $settings, $histories, $fineTunnel);
    }

    /**
     * Handle AI response based on intent
     */
    private function handleAIResponse($responseBody, WhatsappKeyAccount $device, Setting $settings, $histories, $fineTunnel): mixed
    {
        $intent = json_decode($responseBody->choices[0]->message->content);
        $creditUsed = $responseBody->usage->total_tokens ?? 0;

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
                $replyMessage = $this->handleMediaIntent($intent, $histories, $device);
                break;

            case 'check_shipping':
                $replyMessage = $this->handleShippingIntent($intent, $histories, $fineTunnel, $settings);
                break;

            case 'question':
                $replyMessage = $this->handleQuestionIntent($intent, $histories);
                break;
        }

        // Update credits
        if ($creditUsed > 0) {
            $this->updateCredits($creditUsed, $device, $fineTunnel, $replyMessage);
        }

        // Check for transfer conditions
        $this->checkTransferConditions($device, $histories, $intent->message ?? '');

        return $replyMessage;
    }

    /**
     * Handle media intent
     */
    private function handleMediaIntent($intent, $histories, WhatsappKeyAccount $device): mixed
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

            $this->sendMediaMessageToUser($device, $histories, $replyMessage, $typeMessage);
            $lastReplyMessage = $replyMessage;
        }

        return $lastReplyMessage;
    }

    /**
     * Handle shipping check intent
     */
    private function handleShippingIntent($intent, $histories, $fineTunnel, Setting $settings): mixed
    {
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

        $this->sendTextMessageToUser($histories->waba, $histories, $replyText, $reply);

        return $reply;
    }

    /**
     * Handle question intent
     */
    private function handleQuestionIntent($intent, $histories): mixed
    {
        $replyText = $this->cleanText($intent->message);

        $reply = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => 'yes',
            'message' => $replyText
        ]);

        $this->sendTextMessageToUser($histories->waba, $histories, $replyText, $reply);
        return $reply;
    }

    /**
     * Process any-chat auto-reply
     */
    private function processAnyReply(WhatsappKeyAccount $device, $histories): mixed
    {
        $reply = $this->anyAutoReply($device, $histories->name);

        if (!$reply['status']) {
            return null;
        }

        $followUps = $device->finetunnel ? $device->finetunnel->follow_ups->count() : 0;

        $replyMessage = $histories->details()->create([
            'history_chat_id' => $histories->id,
            'from' => 'device',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => $reply['message_text']
        ]);

        $this->sendTextMessageToUser($device, $histories, $reply['message_text'], $replyMessage);

        return $replyMessage;
    }

    /**
     * Check if device has credits
     */
    private function hasCredits(WhatsappKeyAccount $device): bool
    {
        if (!($device->business->merchant ?? null)) {
            return true;
        }

        $topupLimit = $device->business->package_active_topup->sisa_credit ?? 0;
        $packageCredit = $device->business->package_active->sisa_credit ?? 0;

        return $topupLimit > 0 || $packageCredit > 0;
    }

    /**
     * Get message variables for WhatsApp API
     */
    private function getMessageVariables(WhatsappKeyAccount $device): array
    {
        $config = $device->meta_data ? json_decode($device->meta_data, true) : [];

        return [
            'appid' => $config['whatsapp']['app_id'] ?? null,
            'phoneid' => $config['whatsapp']['phone_number_id'] ?? null,
            'wabaid' => $config['whatsapp']['waba_id'] ?? null,
            'access_token' => $config['whatsapp']['access_token'] ?? null,
        ];
    }

    /**
     * Send text message to user
     */
    private function sendTextMessageToUser(WhatsappKeyAccount $device, $histories, string $message, $replyMessage): void
    {
        $messageVariable = $this->getMessageVariables($device);

        $sendMessage = $this->whatsappServiceObserver->sendTextMessage($histories->from_number, $message, $messageVariable);
        if ($sendMessage['status'] === 200) {
            $replyMessage->update(['messageid' => $sendMessage['messageid']]);
        }
    }

    /**
     * Send media message to user
     */
    private function sendMediaMessageToUser(WhatsappKeyAccount $device, $histories, $replyMessage, string $typeMessage): void
    {
        $messageVariable = $this->getMessageVariables($device);
        $sendMedia = $this->whatsappServiceObserver->uploadMedia(
            $messageVariable['access_token'],
            $replyMessage->file_path,
            $typeMessage,
            $messageVariable['phoneid']
        );

        if ($sendMedia) {
            $sendMessage = $this->whatsappServiceObserver->sendMediaMessage(
                $histories->store->phone,
                $typeMessage,
                $sendMedia,
                $replyMessage->message,
                $replyMessage->file_path,
                $messageVariable
            );

            if ($sendMessage['status'] === 200) {
                $replyMessage->update(['messageid' => $sendMessage['messageid']]);
            }
        }
    }



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

    private function updateCredits(int $creditUsed, WhatsappKeyAccount $device, $fineTunnel, $replyMessage): void
    {
        if ($creditUsed <= 0 || is_null($device->business->merchant ?? null)) {
            return;
        }

        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $totalCreditUsing = $this->calculateCreditUsage($creditUsed, $fineTunnel->model_ai, $setting);

        $this->deductCredits($device, $totalCreditUsing);

        if ($replyMessage) {
            $replyMessage->update(['credit_using' => $totalCreditUsing]);
        }
    }

    private function calculateCreditUsage(int $creditUsed, string $modelAi, $setting): int
    {
        $creditPer250Tokens = 1;
        $tokensPerCredit = $modelAi === 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($creditUsed / $tokensPerCredit) * $creditPer250Tokens;
    }

    private function deductCredits(WhatsappKeyAccount $device, int $totalCreditUsing): void
    {
        $packageCredit = $device->business->package_active->sisa_credit ?? 0;

        if ($packageCredit > 0) {
            $device->business->package_active->update([
                'using_credit_limit' => $device->business->package_active->using_credit_limit + $totalCreditUsing
            ]);
        } else {
            $device->business->package_active_topup->update([
                'using_credit_limit' => $device->business->package_active_topup->using_credit_limit + $totalCreditUsing
            ]);
        }
    }

    private function checkTransferConditions(WhatsappKeyAccount $device, $histories, string $message): void
    {
        if ($histories->takeover !== 'no') {
            return;
        }

        $termCondition = $device->finetunnel->transfer_condition ?? null;
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

    private function notifyAgents(WhatsappKeyAccount $device, $histories, string $message): void
    {
        $agentString = $device->finetunnel->agent ?? null;
        if (!$agentString) {
            return;
        }

        $agentIds = array_map('trim', explode(',', $agentString));
        foreach ($agentIds as $agentId) {
            $user = User::find($agentId);
            if ($user) {
                // $this->sendToAgent($histories, $message, $user, $device);
                // Implementation can be added here
            }
        }
    }

    function cleanText($text)
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


    /*
    |--------------------------------------------------------------------------
    | 3. Validation For Auto Reply Days and Time
    |--------------------------------------------------------------------------
    */

    public function checkingTimeAutoReply(WhatsappKeyAccount $device)
    {

        /**
         * Check For Activation Days 
         */

        if ($device->auto_reply_certain_day == 'yes') {
            if ($device->days != null) {
                $day        = date("D");
                $getVoucher = WhatsappKeyAccount::where("id", $device->id)->whereRaw("find_in_set('" .  $day . "',days)")->count(); // Check Day in This Auto Reply

                // If Auto Reply Not Active for this day
                if ($getVoucher == 0) {
                    return false;
                }
            }
        }


        /**
         * Checking For Activation Time
         */

        if ($device->auto_reply_certain_time == 'yes') {
            if ($device->start_time != null) {
                if ($device->start_time > date("H:i")) {
                    return false;
                }

                if ($device->end_time < date("H:i")) {
                    return false;
                }
            }
        }


        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Validation For Auto Reply Days and Time
    |--------------------------------------------------------------------------
    */

    public function downloadMedia(WhatsappKeyAccount $device, $mediaId, $mimeType)
    {

        $config             = $device->meta_data;
        $config             = $config ? json_decode($config, true) : [];
        $accessToken        = $config['whatsapp']['access_token'] ?? null;

        if (!$accessToken || !$mediaId) {
            return null;
        }

        $response           = Http::withToken($accessToken)->get("https://graph.facebook.com/v20.0/{$mediaId}");
        $mediaUrl           = $response->json()['url'] ?? null;

        if (!$mediaUrl) {
            return null;
        }

        $fileResponse = Http::withToken($accessToken)->get($mediaUrl);
        if (!$fileResponse->successful()) {
            return null;
        }

        $fileContent = $fileResponse->body();

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'video/mp4'  => 'mp4',
            'audio/mpeg' => 'mp3',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip'
        ];

        $extension  = $extensionMap[$mimeType] ?? 'bin';
        $filename   = $mediaId . '.' . $extension;
        $path       = 'uploads/media-manager/' . $filename;

        Storage::disk('local')->put($path, $fileContent);

        return [
            'path' => $path,
            'mime' => $mimeType,
            'size' => strlen($fileContent), // byte
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Whatsapp Message For Use Auto Reply
    |--------------------------------------------------------------------------
    */

    public function autoReplyMessage(WhatsappKeyAccount $device, $message, $name = '', $from, $type = 'personal')
    {
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $device->id . "',select_device)")->where('meta_account_id', '!=', null)->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        if ($chatBot->reply_method == 'text') {
            $messageText = str_replace('{name}', $name ?? '', $chatBot->message);

            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'method'        => $chatBot->reply_method,
                'message'       => array(
                    'text'          => $messageText
                )
            );
        }

        if ($chatBot->reply_method == 'template') {

            // $templateBuilder = $this->whatsappTemplateServiceObserver->buildTemplateChatbot($chatBot);
            // $file           = $chatBot->template->image != null ? asset($chatBot->template->image) : '';
            // $messageText    = $chatBot->template->message ?? '';
            // $messageData    = $this->whatsappServiceObserver->formatDataMessage($messageText, $file, $chatBot->template->type_content, json_decode($chatBot->template->button_or_list, true));
            // return array(
            //     'status'        => true,
            //     'method'        => $chatBot->reply_method,
            //     'message_text'  => $templateBuilder,
            //     'message'       => $messageData
            // );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Ai Reply
    |--------------------------------------------------------------------------
    */

    public function aiReply(WhatsappKeyAccount $device, $message, $from, $type = 'personal', $key, $option, HistoryChat $historyChat, $dataTrainings, $forCheckOngkir)
    {
        // Checking FineTunnel is Ready
        if (!$device->finetunnel) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        $status         = false;
        $topupLimit     = 0;
        $packageCredit  = 0;
        $usageCredit    = 0;

        if (($device->business->merchant ?? null) != null) {
            $topupLimit     = $device->business->package_active_topup->sisa_credit ?? 0;
            $packageCredit  = $device->business->package_active->sisa_credit ?? 0;

            if ($topupLimit > 0 || $packageCredit > 0) {
                $status = true;
            }
        } else {
            $status = true;
        }


        if (!$status) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        $fineTunnel     = $device->finetunnel;
        $modelAi        = $fineTunnel->model_ai;
        $setting        = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $conversations  = $historyChat->details_desc->take($fineTunnel->history_limit)->sortBy('created_at');
        $response       = null;
        $type           = 'message';

        if ($option == 'chatgpt') {
            $result       = $this->openAiServiceObserver->getQuestion($key, $message, ($fineTunnel->description ?? ''), $conversations, $modelAi, $dataTrainings, $forCheckOngkir);
            if ($result->status() == 200) {
                $responseBody   = json_decode($result->body());
                if (isset($responseBody->choices[0])) {
                    $response   = $responseBody->choices[0]->message->content;

                    if ($response == null) {
                        if (isset($responseBody->choices[0]->message->tool_calls)) {
                            $type       = 'function';
                            $response   = $responseBody->choices[0]->message->tool_calls;
                        }
                    }
                }

                if (isset($responseBody->usage->total_tokens) && ($device->business->merchant ?? null) != null) {
                    $tokenUsage     = $responseBody->usage->total_tokens;
                    if ($tokenUsage > 0) {
                        if ($modelAi == 'standart') {
                            $creditPer250Tokens = 1;
                            $tokensPerCredit = $setting->credit_token_basic;

                            $usageCredit = ceil($tokenUsage / $tokensPerCredit) * $creditPer250Tokens;
                        }

                        if ($modelAi == 'advanced') {
                            $creditPer250Tokens = 1;
                            $tokensPerCredit = $setting->credit_token_advance;

                            $usageCredit = ceil($tokenUsage / $tokensPerCredit) * $creditPer250Tokens;
                        }
                    }

                    if ($usageCredit > 0) {
                        if ($packageCredit > 0) {
                            $device->business->package_active->update([
                                'using_credit_limit'        => $device->business->package_active->using_credit_limit + $usageCredit
                            ]);
                        } else {
                            $device->business->package_active_topup->update([
                                'using_credit_limit'        => $device->business->package_active_topup->using_credit_limit + $usageCredit
                            ]);
                        }
                    }
                }
            }
        }


        if ($response != null) {
            return array(
                'status'    => true,
                'credit'    => $usageCredit,
                'message'   => $response,
                'type'      => $type
            );
        }


        return array(
            'status'    => false,
            'message'   => null
        );
    }



    /*
    |--------------------------------------------------------------------------
    | 5. Any Auto Reply
    |--------------------------------------------------------------------------
    */

    public function anyAutoReply(WhatsappKeyAccount $device, $name)
    {

        if ($device->reply_method == 'text') {

            $message = str_replace(
                ['{whatsapp_name}'],
                [$name],
                $device->reply_text
            );

            return array(
                'status'        => true,
                'message_text'  => $message,
                'message'       => array(
                    'text'          => $message
                )
            );
        }
    }

    public function sendCustomWebHook($from, $message = null, $file, $messageType, WhatsappKeyAccount $device)
    {
        return Http::accept('application/json')->post($device->webhook, [
            'device_key'    => $device->id,
            'from'          => $from,
            'message'       => $message,
            'type'          => $messageType,
            'file'          => $file
        ]);
    }

    private function getMediaInfo($url)
    {
        // Ambil informasi path dan ekstensi dari URL
        $pathInfo = pathinfo($url);

        // Ambil headers dari URL
        $headers = get_headers($url, 1);

        // Ambil size dan type dari headers (jika tersedia)
        $size = isset($headers['Content-Length']) ? (int) $headers['Content-Length'] : null;
        $type = isset($headers['Content-Type']) ? $headers['Content-Type'] : null;

        return [
            'type' => $type,
            'size' => $size,
            'path' => $pathInfo['dirname'] . '/' . $pathInfo['basename'],
        ];
    }


    public function triggerEmit($replyForEmit, $userForEmit, $welcomeForEmit)
    {

        $expressUrl = config('services.express.url') . '/trigger-whatsapp';

        if ($welcomeForEmit) {
            Http::post($expressUrl, HistoryChatResources::make($welcomeForEmit));
        }

        if ($userForEmit) {
            // Kirim request pertama
            $response = Http::post($expressUrl, HistoryChatResources::make($userForEmit));

            // Jika request pertama berhasil, baru kirim request kedua
            if ($response->successful() && $replyForEmit) {
                Http::post($expressUrl, HistoryChatResources::make($replyForEmit));
            }
        } elseif ($replyForEmit) {
            // Jika $userForEmit tidak ada, langsung kirim request kedua
            Http::post($expressUrl, HistoryChatResources::make($replyForEmit));
        }
    }

    public function checkStorage($setting, $fileSize = 0)
    {
        if ($setting->merchant) {
            $totalSize  = 0;
            $path       = "uploads/folders/{$setting->id}";

            if (Storage::disk('local')->exists($path)) {
                $files = Storage::disk('local')->allFiles($path);
                foreach ($files as $file) {
                    $totalSize += Storage::disk('local')->size($file);
                }
            }

            // Convert to MB
            $usedStorageMB  = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB     = round($fileSize / 1024 / 1024, 2);

            // Get total storage
            $storageFromSubscribe   = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons      = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage           = $storageFromSubscribe + $storageFromAddons;

            // Check if storage is available
            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available'         => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage'     => $totalStorage,
                'used_storage'      => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size'         => $fileSizeMB,
                'has_package'       => $totalStorage > 0
            ];
        } else {
            return [
                'available'         => true,
                'total_storage'     => 9999999,
                'used_storage'      => 0,
                'remaining_storage' => 9999,
                'file_size'         => 9999,
                'has_package'       => 9999
            ];
        }
    }

    private function ensureDirectoryExists($path)
    {

        if (!file_exists('uploads/' . $path)) {
            mkdir('uploads/' . $path, 0755, true);
        }
    }

    private function getSubFolderByMimeType(string $mimeType): string
    {
        // Ekstrak tipe utama dari mime type (contoh: image/jpeg -> image)
        $mainType = explode('/', $mimeType)[0];

        switch ($mainType) {
            case 'image':
                return 'wa-images';

            case 'video':
                return 'wa-video';

            case 'audio':
                return 'wa-audio';

            case 'application':
            case 'text':
            default:
                return 'wa-document';
        }
    }
}
