<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Crm\MessagestResource;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\HistoryChat;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\InternalSetting;
use App\Models\Master\Label;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Observers\ChatBot\BiteshipServiceObserver;
use App\Observers\ChatBot\ChatPdfServiceObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\HistoryChatObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\ChatBot\RajaOngkirServiceObserver;
use App\Observers\Store\StoreObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Supports\MimeTypes;
use Illuminate\Support\Facades\Storage;

class WhatsappCallbackController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Whatsapp Callback Api Controller
    |--------------------------------------------------------------------------
    */

    protected $openAiServiceObserver;
    protected $whatsappServiceObserver;
    protected $geminiAiServiceObserver;
    protected $chatPdfServiceObserver;
    protected $historyChatObserver;
    protected $storeObserver;
    protected $rajaOngkirObserver;
    protected $biteshipServiceObserver;
    protected $generalSetting;
    protected $typeMimeData;

    public function __construct(
        OpenAiServiceObserver $openAiServiceObserver,
        WhatsappServiceObserver $whatsappServiceObserver,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        HistoryChatObserver $historyChatObserver,
        ChatPdfServiceObserver $chatPdfServiceObserver,
        StoreObserver $storeObserver,
        RajaOngkirServiceObserver $rajaOngkirObserver,
        BiteshipServiceObserver $biteshipServiceObserver
    ) {
        $this->openAiServiceObserver        = $openAiServiceObserver;
        $this->whatsappServiceObserver      = $whatsappServiceObserver;
        $this->geminiAiServiceObserver      = $geminiAiServiceObserver;
        $this->historyChatObserver          = $historyChatObserver;
        $this->chatPdfServiceObserver       = $chatPdfServiceObserver;
        $this->storeObserver                = $storeObserver;
        $this->rajaOngkirObserver           = $rajaOngkirObserver;
        $this->biteshipServiceObserver      = $biteshipServiceObserver;
        $this->generalSetting               = Setting::where('merchant_id', null)->first(['open_ai_key', 'ai_option', 'cek_ongkir_option_api', 'cek_ongkir_api', 'ongkir_method']);
        $this->typeMimeData                 = MimeTypes::TYPE_MAP;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Whatsapp Callback / Webhook
    |--------------------------------------------------------------------------
    */

    public function callBackWhatsapp(Request $request, $device_id)
    {

        $context = $this->initializeContext($request, $device_id);

        if (!$context['device']) {
            return $this->errorResponse();
        }

        try {

            return DB::transaction(function () use ($context, $request) {

                $history = $this->getOrCreateHistory($context);
                $context['history']     = $history;
                if ($this->shouldSkipProcessing($history, $request)) {
                    return $this->createResponse(null, $context, false);
                }

                $this->updateHistoryIfNeeded($history, $context);

                if ($this->shouldSkipDueToReplySettings($context)) {
                    return $this->createResponse(null, $context, false);
                }

                if ($this->hasReachedDailyLimit($context['device'])) {
                    return $this->createResponse(null, $context, false);
                }

                $context['file_data'] = $this->processFileUpload($request, $context, $history);
                $context['message_type'] = $this->determineMessageType($context['file_data']);

                $this->createOrUpdateStore($history, $context['device']);

                $user_message = $this->createUserMessage($request, $history, $context);
                $this->markPreviousMessagesAsFollowUp($history);
                // FIX: Invalidate CRM cache agar sidebar real-time update
                $this->invalidateCrmCache($context['device']->merchant_id);

                $context['message'] = $this->processAudioToText($request, $context, $user_message);

                $reply_data = $this->processAutoReply($request, $context, $history, $user_message);

                $this->triggerEmit($reply_data['reply'], $user_message, $reply_data['welcome']);

                return $this->createResponse(
                    $reply_data['message'],
                    $context,
                    $reply_data['has_reply'],
                    $reply_data['delay'] ?? 0
                );
            });
        } catch (\Exception $e) {
            Log::error('WhatsApp callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse();
        }
    }

    private function initializeContext(Request $request, $device_id): array
    {
        $session = $device_id;
        $device_id = str_replace('device_', '', $device_id);
        $device = WhatsappDevice::find($device_id);

        $request_from   = explode('@', $request->from);
        $request_jid    = explode('@', $request->jid);
        $from_type = $request_from[1] == 'g.us' ? 'group' : 'personal';

        $business_setting = null;
        if ($device) {
            $business_setting = Setting::where('id', $device->business_id)->first(['history_ai_chat_option', 'history_ai_chat', 'is_online', 'id', 'cek_ongkir_api']);
        }

        return [
            'session'           => $session,
            'jid_number'        => $from_type == 'personal' ? $request_jid[0] : null,
            'meta_data'         => $request->from,
            'device'            => $device,
            'request_from'      => $request_from,
            'from_type'         => $from_type,
            'business_setting'  => $business_setting,
            'avatar_url'        => $request->avatar_url,
            'message'           => $request->message,
            'follow_ups_count'  => $device?->finetunnel?->follow_ups?->count() ?? 0,
            'file_data'         => $this->initializeFileData(),
            'message_type'      => 'text'
        ];
    }

    private function errorResponse()
    {
        return response()->json([
            'message'       => ['text' => null],
            'delay'         => 0,
            'receiver'      => '',
            'session_id'    => '',
            'autoread'      => false,
            'reply'         => false
        ], 500);
    }

    private function getOrCreateHistory(array $context)
    {
        $history = $this->historyChatObserver->getByNumber(
            $context['from_type'],
            $context['request_from'][0],
            $context['device']->id
        );

        if (!$history) {
            $history = HistoryChat::create([
                'device_id'         => $context['device']->id,
                'avatar_url'        => $context['avatar_url'],
                'name'              => request()->from_name,
                'merchant_id'       => $context['device']->merchant_id,
                'type'              => $context['from_type'],
                'jid_number'        => $context['jid_number'],
                'from_number'       => $context['request_from'][0],
                'business_id'       => $context['device']->business_id,
                'from'              => 'whatsapp',
                'takeover'          => 'no',
                'status'            => 'open',
                'label'             => null,
                'metadata'          => $context['meta_data']
            ]);
        }

        return $history;
    }

    private function shouldSkipProcessing($history, Request $request): bool
    {
        if (!$history) {
            return false;
        }

        return $history->status == 'block' || $history->details()->where('messageid', $request->message_id)->exists();
    }


    private function createResponse($message, array $context, bool $has_reply, int $delay = 0)
    {

        // Normalize message format
        if (is_string($message)) {
            $normalized_message = $this->normalizeMessage($message);
        } else {
            if (is_null($message)) {
                $normalized_message = $this->normalizeMessage($message);
            } else {
                $normalized_message = $message;
            }
        }

        // Auto-detect reply status if message is empty
        $actual_has_reply = $this->determineReplyStatus($normalized_message, $has_reply);

        return response()->json([
            'message' => $normalized_message,
            'delay' => $delay,
            'receiver' => $actual_has_reply ? request()->from : '',
            'session_id' => $context['session'],
            'autoread' => $context['device']->auto_read_before_autorespon == 'yes',
            'reply' => $actual_has_reply,
        ], 200);
    }

    private function normalizeMessage($message): array
    {
        if (is_null($message) || $message === '' || $message === []) {
            return ['text' => null];
        }

        $media_keys = ['image', 'video', 'audio', 'document'];
        if (is_array($message)) {
            foreach ($media_keys as $key) {
                if (array_key_exists($key, $message)) {
                    return $message;
                }
            }
        }

        $template_keys = ['buttons', 'sections', 'poll', 'location'];
        if (is_array($message)) {
            foreach ($template_keys as $key) {
                if (array_key_exists($key, $message)) {
                    return $message;
                }
            }
        }

        if (is_array($message) && array_key_exists('text', $message)) {
            return $message;
        }

        if (is_string($message)) {
            return ['text' => trim($message) ?: null];
        }

        if (is_array($message)) {
            return ['text' => isset($message[0]) ? $message[0] : null];
        }

        return ['text' => null];
    }

    private function determineReplyStatus(array $normalized_message, bool $original_has_reply): bool
    {

        $media_keys = ['image', 'video', 'audio', 'document'];
        foreach ($media_keys as $key) {
            if (array_key_exists($key, $normalized_message)) {
                return true;
            }
        }

        $template_keys = ['buttons', 'sections', 'poll', 'location'];
        foreach ($template_keys as $key) {
            if (array_key_exists($key, $normalized_message)) {
                return true;
            }
        }

        if (array_key_exists('text', $normalized_message)) {
            $text = $normalized_message['text'];
            return !is_null($text) && trim($text) !== '';
        }

        if (array_key_exists('caption', $normalized_message)) {
            $caption = $normalized_message['caption'];
            return !is_null($caption) && trim($caption) !== '';
        }

        return false;
    }

    private function updateHistoryIfNeeded($history, array $context): void
    {
        if (!$history || $history->status == 'block') {
            return;
        }

        if ($history->status != 'open') {
            $history->update(['status' => 'open']);
        }

        if ($context['avatar_url'] && $history->avatar_url !== $context['avatar_url']) {
            $history->update(['avatar_url' => $context['avatar_url']]);
        }

        if ($context['jid_number'] && $history->jid_number !== $context['jid_number']) {
            $history->update(['jid_number' => $context['jid_number']]);
        }

        if ($context['request_from'][0] && $history->from_number !== $context['request_from'][0]) {
            $history->update(['from_number' => $context['request_from'][0] ?? $context['jid_number']]);
        }

        if ($context['meta_data'] && $history->meta_data !== $context['meta_data']) {
            $history->update(['metadata' => $context['meta_data']]);
        }
    }

    private function shouldSkipDueToReplySettings(array $context): bool
    {
        $device = $context['device'];
        $from_type = $context['from_type'];

        return ($device->auto_reply_option == 'group' && $from_type == 'personal') ||  ($device->auto_reply_option == 'personal' && $from_type == 'group');
    }

    private function hasReachedDailyLimit($device): bool
    {
        return $device->daily_limit == 'yes' && $device->daily_send >= $device->limit_per_day;
    }

    private function processFileUpload(Request $request, array $context, HistoryChat $history): array
    {
        if (!$this->checkingTimeAutoReply($context['device']) || !$request->hasFile('file')) {
            return $context['file_data'];
        }

        $file       = $request->file('file');
        $fileType   = $file->getMimeType();
        $fileSize   = $file->getSize();


        $storageCheck = $this->checkStorage($history->business, $fileSize);

        if (!$storageCheck['available']) {
            $file_path      = 'images/user.png';
            $fileType       = 'image/png';
            $fileSize       = 0;
        } else {

            $subFolder = $this->getSubFolderByMimeType($fileType);

            $subFolder  = $this->getSubFolderByMimeType($fileType);
            $uploadPath = 'folders/' . $history->business_id . '/' . $subFolder . '/';
            $this->ensureDirectoryExists($uploadPath);
            $file_path  = $this->uploadImage($request, 'file', $uploadPath);
        }

        return [
            'status' => !is_null($file_path),
            'type' => $fileType,
            'size' => $fileSize,
            'path' => $file_path
        ];
    }

    private function determineMessageType(array $file_data): string
    {
        if (!$file_data['status'] || !$file_data['type']) {
            return 'text';
        }

        foreach ($this->typeMimeData as $type => $mime_types) {
            if (in_array($file_data['type'], $mime_types)) {
                return $type;
            }
        }

        return 'text';
    }

    private function createOrUpdateStore($history, $device): void
    {
        $store = $this->storeObserver->checkByNumberAndJid($device->business_id, $history->from_number, $history->jid_number);

        if (!$store) {
            $store = $this->storeObserver->createByHistory($history);
        }

        if (empty($history->store_id)) {
            $history->update(['store_id'    => $store->id]);
        }
    }

    private function createUserMessage(Request $request, $history, array $context)
    {
        $reply_for_chat = null;
        if (isset($request->stanzaId) && !is_null($request->stanzaId)) {
            $reply_for_chat = HistoryChatDetail::where('messageid', $request->stanzaId)->first();
        }

        return $history->details()->create([
            'file_path' => $context['file_data']['path'],
            'file_type' => $context['file_data']['type'],
            'file_size' => $context['file_data']['size'],
            'type' => $context['message_type'],
            'history_chat_id' => $history->id,
            'from' => 'user',
            'message' => $context['message'],
            'remotejid' => $request->from,
            'messageid' => $request->message_id,
            'reply_to' => $reply_for_chat->id ?? null,
            'reply_text' => $reply_for_chat->message ?? null,
            'quoted_message' => is_string($request->quoted_message)
                ? $request->quoted_message
                : (is_null($request->quoted_message) ? null : json_encode($request->quoted_message)),
        ]);
    }

    /**
     * Invalidate CRM contacts cache untuk merchant saat pesan baru masuk
     * Agar sidebar real-time tanpa perlu browser refresh
     *
     * Cache disimpan di DB table 'cache' (bukan Redis),
     * jadi delete langsung via DB query dengan LIKE pattern.
     */
    private function invalidateCrmCache($merchantId): void
    {
        try {
            $prefix = config('cache.prefix', 'replai_automation_cache');
            $pattern = $prefix . '_crm_contacts_' . $merchantId . '_%';
            // Hapus langsung dari cache table di database
            \DB::table('cache')->where('key', 'like', $pattern)->delete();
        } catch (\Exception $e) {
            // Silently fail agar tidak ganggu proses utama
        }
    }

    private function markPreviousMessagesAsFollowUp($history): void
    {
        $history->details()
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->update([
                'is_follow_up' => 'yes',
                'follow_up_id' => null
            ]);
    }

    private function processAudioToText(Request $request, array $context, $user_message): ?string
    {
        $device = $context['device'];
        $business_setting = $context['business_setting'];
        $message = $context['message'];

        if (
            $context['message_type'] !== 'audio' || !in_array($device->auto_reply_method, ['ai', 'all']) || !$this->canProcessAI($context) || !$device->finetunnel
        ) {
            return $message;
        }

        $ai_option  = $this->generalSetting->ai_option ?? 'chatgpt';

        if ($ai_option == 'gemini') {
            $audio_result = $this->geminiAiServiceObserver->checkAudioData(
                $context['file_data']['path'],
                $this->generalSetting->open_ai_key
            );
        } else {
            $audio_result = $this->openAiServiceObserver->checkAudioData(
                $context['file_data']['path'],
                $this->generalSetting->open_ai_key
            );
        }


        return $audio_result['status'] ? $audio_result['message'] : $message;
    }

    private function processAutoReply(Request $request, array $context, $history, $user_message): array
    {
        $device = $context['device'];
        $message = $context['message'];


        if ((is_null($message) && $user_message->type != 'image') || !$this->checkingTimeAutoReply($device)) {
            return $this->createEmptyReplyData();
        }

        if (!empty($message)) {
            $this->processLabels($device, $history, $message);
        }


        // Send welcome message if needed
        $welcome_message = $this->processWelcomeMessage($device, $history, $context);

        // Send custom webhook if configured
        if (!empty($device->webhook)) {
            $this->sendCustomWebHook($request, $device);
        }

        // Process different auto reply methods
        $reply_data = $this->processReplyMethods($device, $history, $context, $user_message);
        $reply_data['welcome'] = $welcome_message;

        return $reply_data;
    }

    private function createEmptyReplyData(): array
    {
        return [
            'has_reply' => false,
            'reply' => null,
            'welcome' => null,
            'message' => ['text' => null],
            'delay' => 0
        ];
    }

    private function processLabels($device, $history, string $message): void
    {
        $fine_tunnel = $device->finetunnel;
        if (!$fine_tunnel || !$fine_tunnel->label) {
            return;
        }

        $label_ids = array_map('trim', explode(',', $fine_tunnel->label));

        $matching_label = Label::where('business_id', $device->business_id)
            ->where('type', 'keyword')
            ->whereIn('id', $label_ids)
            ->whereRaw("? REGEXP REPLACE(tag, ', ', '|')", [$message])
            ->first(['id', 'name']);

        if (!$matching_label || !$history) {
            return;
        }

        $current_labels = json_decode($history->label, true) ?? [];
        $already_exists = collect($current_labels)->contains('id', $matching_label->id);

        if (!$already_exists) {
            $current_labels[] = [
                'id' => $matching_label->id,
                'name' => $matching_label->name,
            ];

            $history->update(['label' => json_encode($current_labels)]);
            if ($history->store) {
                if ($history->store->label_id == null) {
                    $history->store->update([
                        'label_id'      => $matching_label->id
                    ]);
                }
            }
        }
    }

    private function processWelcomeMessage($device, $history, array $context)
    {
        $fine_tunnel = $device->finetunnel;

        if (
            !$fine_tunnel ||
            !$fine_tunnel->welcome_message ||
            $history->details->count() != 1
        ) {
            return null;
        }

        $welcome_data = $this->prepareWelcomeMessageData($fine_tunnel);
        $messageText    = str_replace('{name}', $history->name ?? '', $fine_tunnel->welcome_message ?? '');
        $welcome_message = $history->details()->create([
            'message' => $messageText,
            'file_path' => $welcome_data['file']['path'],
            'file_type' => $welcome_data['file']['type'],
            'file_size' => $welcome_data['file']['size'],
            'type' => $welcome_data['message_type'],
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
        ]);

        $this->sendWelcomeMessage($welcome_message, $context);

        return $welcome_message;
    }

    private function initializeFileData(): array
    {
        return [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];
    }

    private function prepareWelcomeMessageData($fine_tunnel): array
    {
        $message_type = 'text';
        $file_data = $this->initializeFileData();

        if (!file_exists($fine_tunnel->welcome_image)) {
            return ['file' => $file_data, 'message_type' => $message_type];
        }

        $file_path = public_path($fine_tunnel->welcome_image);
        $file_type = mime_content_type($file_path);
        $file_size = filesize($file_path);

        $file_data = [
            'status' => true,
            'type' => $file_type,
            'size' => $file_size,
            'path' => $fine_tunnel->welcome_image
        ];

        if ($file_type) {
            foreach ($this->typeMimeData as $key => $mime_types) {
                if (in_array($file_type, $mime_types)) {
                    $message_type = $key;
                    break;
                }
            }
        }

        return ['file' => $file_data, 'message_type' => $message_type];
    }

    private function sendWelcomeMessage($welcome_message, array $context): void
    {
        $file_url = $welcome_message->file_path ? asset($welcome_message->file_path) : '';

        $this->whatsappServiceObserver->sendMessage(
            ($context['meta_data'] ?? $context['request_from'][0]),
            $context['device']->id,
            $welcome_message->message,
            $file_url,
            'description',
            null,
            $context['from_type'] == 'group'
        );
    }

    private function processReplyMethods($device, $history, array $context, $user_message): array
    {
        $message = $context['message'];
        $from_type = $context['from_type'];
        $follow_ups_count = $context['follow_ups_count'];

        // Try chatbot first
        if (in_array($device->auto_reply_method, ['chatbot', 'all']) && $history->status != 'block') {
            $chatbot_reply = $this->processChatbotReply($device, $message, $context, $history, $follow_ups_count);
            if ($chatbot_reply['has_reply']) {
                return $chatbot_reply;
            }
        }

        if (in_array($device->auto_reply_method, ['ai', 'all']) && $this->canProcessAI($context)) {
            $ai_reply = $this->processAIReply($device, $history, $context, $follow_ups_count);
            if ($ai_reply['has_reply']) {
                return $ai_reply;
            }
        }

        // Try any chat reply
        if ($device->reply_any_chat == 'yes' && $history->details->count() == 0) {
            $any_reply = $this->processAnyReply($device, $context, $history, $follow_ups_count);
            if ($any_reply['has_reply']) {
                return $any_reply;
            }
        }

        return $this->createEmptyReplyData();
    }

    private function processChatbotReply($device, $message, array $context, $history, int $follow_ups_count): array
    {
        $reply = $this->autoReplyMessage(
            $device,
            $message,
            $history->name,
            ($context['meta_data'] ?? $context['request_from'][0]),
            $context['from_type']
        );

        if (!$reply['status']) {
            return ['has_reply' => false];
        }

        $reply_message = $history->details()->create([
            'file_path'             => $reply['image'] ?? null,
            'file_type'             => $reply['file_type'] ?? null,
            'file_size'             => $reply['file_size'] ?? null,
            'type'                  => $reply['image'] ? 'image' : 'text',
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
            'message' => $reply['message_text'],
        ]);

        return [
            'has_reply' => true,
            'reply' => $reply_message,
            'message' => $reply['message'],
            'delay' => 0
        ];
    }

    private function processAIReply($device, $history, array $context, int $follow_ups_count): array
    {

        $fine_tunnel = $device->finetunnel;
        $business_setting = $context['business_setting'];
        $message = $context['message'];
        $message_type = $context['message_type'];
        $file_data = $context['file_data'];

        $delay_response = (int)($fine_tunnel->delay ?? 0) * 1000;

        if (!$this->hasValidCredits($device)) {
            return ['has_reply' => false];
        }

        $intent_data = $this->detectIntent($device, $history, $message ?? '', $message_type, $file_data);

        if (!$intent_data['success']) {
            return ['has_reply' => false];
        }

        $intent = $intent_data['intent'];
        $credit_used = $intent_data['credit_used'];

        // Handle different intents
        $reply_result = $this->handleIntent($intent, $history, $device, $context, $follow_ups_count);

        if ($reply_result['reply']) {
            $this->updateCredits($credit_used, $device, $fine_tunnel, $reply_result['reply']);
            $this->handleTakeover($device, $history, $message);
        }

        return [
            'has_reply' => !is_null($reply_result['reply']),
            'reply' => $reply_result['reply'],
            'message' => $reply_result['message'],
            'delay' => $delay_response
        ];
    }

    private function processAnyReply($device, array $context, $history, int $follow_ups_count): array
    {
        $reply = $this->anyAutoReply($device, request()->from_name);

        if (!$reply['status']) {
            return ['has_reply' => false];
        }

        $reply_message = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
            'message' => $reply['message_text']
        ]);

        return [
            'has_reply' => true,
            'reply' => $reply_message,
            'message' => $reply['message'],
            'delay' => 0
        ];
    }

    private function detectIntent($device, $history, string $message, string $message_type, array $file_data): array
    {
        $fine_tunnel = $device->finetunnel;
        $conversations = $history->details_desc
            ->take($fine_tunnel->history_limit)
            ->sortBy('created_at');

        $image_path = $message_type == 'image' ? asset($file_data['path']) : null;
        $ai_option  = $this->generalSetting->ai_option ?? 'chatgpt';

        if ($ai_option === 'gemini') {
            return $this->detectIntentWithGemini(
                $fine_tunnel,
                $message ?? '',
                $conversations,
                $fine_tunnel->model_ai,
                $image_path
            );
        }

        return $this->detectIntentWithOpenAI(
            $fine_tunnel,
            $message ?? '',
            $conversations,
            $fine_tunnel->model_ai,
            $image_path
        ); 
    }

    private function detectIntentWithOpenAI(
        $fine_tunnel,
        string $message,
        $conversations,
        string $model_ai,
        $image_path
    ): array {
        $intent_response = $this->openAiServiceObserver->detectIntent(
            $fine_tunnel,
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
     * Detect intent using Gemini
     */
    private function detectIntentWithGemini(
        $fine_tunnel,
        string $message,
        $conversations,
        string $model_ai,
        $image_path
    ): array {
        $gemini_key = $this->generalSetting->open_ai_key ?? null;

        if (empty($gemini_key)) {
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }

        try {
            $intent_response = $this->geminiAiServiceObserver->detectIntent(
                $fine_tunnel,
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
            return ['success' => false, 'intent' => null, 'credit_used' => 0];
        }
    }


    private function handleIntent($intent, $history, $device, array $context, int $follow_ups_count): array
    {


        if (is_object($intent) && isset($intent->properties) && is_object($intent->properties)) {
            foreach ($intent->properties as $key => $value) {
                $intent->{$key} = $value;
            }
            unset($intent->properties);
        }

        if (is_string($intent)) {
            $intent = json_decode($intent);
        }

        switch ($intent->intent) {
            case 'media':
                return $this->handleMediaIntent($intent, $history, $device, $context, $follow_ups_count);

            case 'check_shipping':
                return $this->handleCheckShippingIntent($intent, $history, $device, $context, $follow_ups_count);

            case 'question':
                return $this->handleQuestionIntent($intent, $history, $follow_ups_count);

            default:
                return ['reply' => null, 'message' => ['text' => null]];
        }
    }

    private function handleQuestionIntent($intent, $history, int $follow_ups_count): array
    {
        $reply_message = $this->cleanText($intent->message);

        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
            'message' => $reply_message
        ]);

        return [
            'reply' => $reply,
            'message' => ['text' => $reply_message]
        ];
    }

    private function canProcessAI(array $context): bool
    {
        $history = $context['history'] ?? null;
        $business_setting = $context['business_setting'];
        $device = $context['device'];

        return ($history ? trim(strtolower($history->takeover)) === 'no' : true) &&
            ($history ? $history->status != 'block' : true) &&
            $business_setting &&
            $business_setting->is_online === 'yes' &&
            $device->finetunnel;
    }

    private function hasValidCredits($device): bool
    {
        if (is_null($device->business->merchant ?? null)) {
            return true;
        }

        $topup_limit = $device->business->package_active_topup->sisa_credit ?? 0;
        $package_credit = $device->business->package_active->sisa_credit ?? 0;

        return $topup_limit > 0 || $package_credit > 0;
    }

    private function updateCredits(int $credit_used, $device, $fine_tunnel, $reply_message): void
    {
        if ($credit_used <= 0 || is_null($device->business->merchant ?? null)) {
            return;
        }

        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $total_credit_using = $this->calculateCreditUsage($credit_used, $fine_tunnel->model_ai, $setting);

        $this->deductCredits($device, $total_credit_using);

        if ($reply_message) {
            $reply_message->update(['credit_using' => $total_credit_using]);
        }
    }

    private function calculateCreditUsage(int $credit_used, string $model_ai, $setting): int
    {
        $ai_option = $this->generalSetting->ai_option ?? 'openai';

        if ($ai_option === 'gemini') { 
            return $this->geminiAiServiceObserver->calculateCompletions(
                $model_ai,
                $credit_used,
                0 
            );
        }
        
        $credit_per_250_tokens = 1;
        $tokens_per_credit = $model_ai == 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($credit_used / $tokens_per_credit) * $credit_per_250_tokens;
    }

    private function deductCredits($device, int $total_credit_using): void
    {
        $package_credit = $device->business->package_active->sisa_credit ?? 0;

        if ($package_credit > 0) {
            $device->business->package_active->update([
                'using_credit_limit' => $device->business->package_active->using_credit_limit + $total_credit_using
            ]);
        } else {
            $device->business->package_active_topup->update([
                'using_credit_limit' => $device->business->package_active_topup->using_credit_limit + $total_credit_using
            ]);
        }
    }

    private function handleTakeover($device, $history, string $message): void
    {
        if ($history->takeover !== 'no') {
            return;
        }

        $fine_tunnel = $device->finetunnel;
        $term_condition = $fine_tunnel->transfer_condition ?? null;

        if (!$term_condition) {
            return;
        }

        $keywords = array_map('trim', explode(',', $term_condition));
        $term_text = strtolower($message);

        foreach ($keywords as $keyword) {
            if (stripos($term_text, strtolower($keyword)) === false) {
                continue;
            }

            $history->update(['takeover' => 'yes']);

            $this->notifyAgents($fine_tunnel, $history, $message, $device);
            break;
        }
    }

    private function notifyAgents($fine_tunnel, $history, string $message, $device): void
    {
        $agent_string = $fine_tunnel->agent ?? null;

        if (!$agent_string) {
            return;
        }

        $agent_ids = array_map('trim', explode(',', $agent_string));

        foreach ($agent_ids as $agent_id) {
            $user = User::find($agent_id);

            if ($user) {
                $this->sendToAgent($history, $message, $user, $device);
            }
        }
    }

    private function handleMediaIntent($intent, $history, $device, array $context, $follow_ups_count): array
    {
        $media_urls = $intent->medias ?? [];
        $message = $intent->message ?? '';
        $reply_message = null;

        if (count($media_urls) > 0) {
            foreach ($media_urls as $media_index => $media_url) {
                $media_info = $this->getMediaInfo($media_url);

                if (!empty($media_info['type'])) {
                    $media_type = $this->determineMessageTypeFromMime($media_info['type']);

                    $reply_message = $history->details()->create([
                        'file_path' => $media_url,
                        'file_type' => $media_info['type'],
                        'file_size' => $media_info['size'],
                        'history_chat_id' => $history->id,
                        'from' => 'device',
                        'is_read' => 'yes',
                        'type' => $media_type,
                        'is_follow_up' => 'yes',
                        'message' => $message,
                        'credit_using' => 0,
                    ]);
                } else {

                    $reply_message = $history->details()->create([
                        'history_chat_id' => $history->id,
                        'from' => 'device',
                        'is_read' => 'yes',
                        'type' => 'description',
                        'is_follow_up' => 'yes',
                        'message' => $message,
                        'credit_using' => 0,
                    ]);
                }


                $this->sendMediaMessage($reply_message, $context, $message, $media_url);
            }

            return [
                'reply' => $reply_message,
                'message' => ['text' => null]
            ];
        } else {

            $reply_message = $this->cleanText($intent->message);

            $reply = $history->details()->create([
                'history_chat_id' => $history->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
                'message' => $reply_message
            ]);

            return [
                'reply' => $reply,
                'message' => ['text' => $reply_message]
            ];
        }
    }

    private function sendMediaMessage($reply_message, array $context, string $message, string $media_url): void
    {
        $response_data = $this->whatsappServiceObserver->sendMessage(
            $context['meta_data'] ?? $context['request_from'][0],
            $context['device']->id,
            $message,
            $media_url,
            'description',
            null,
            $context['from_type'] == 'group'
        );

        if ($response_data['status'] == 200) {
            $reply_message->update([
                'messageid' => $response_data['data']['key']['id'],
                'remotejid' => $response_data['data']['key']['remoteJid'],
                'quoted_message' => $response_data['data'],
            ]);
        }
    }

    private function handleCheckShippingIntent($intent, $history, $device, array $context, int $follow_ups_count): array
    {
        $fine_tunnel = $device->finetunnel;
        $business_setting = $context['business_setting'];

        $shipping_config = $this->getShippingConfig($fine_tunnel, $business_setting);

        if (!$shipping_config['available']) {
            return $this->createShippingUnavailableReply($history, $follow_ups_count);
        }

        $reply_message = $this->processShippingRequest($intent, $shipping_config, $fine_tunnel, $context);

        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
            'message' => $reply_message,
        ]);

        return [
            'reply' => $reply,
            'message' => ['text' => $reply_message]
        ];
    }

    private function getShippingConfig($fine_tunnel, $business_setting): array
    {
        $has_package_access = ($business_setting->package_active &&
            $business_setting->package_active->cek_ongkir == 'yes') ||
            is_null($business_setting->merchant_id);

        $has_required_config = $fine_tunnel->couriers->count() > 0 &&
            !empty($fine_tunnel->zip_code) &&
            (int)$fine_tunnel->weight > 0;

        return [
            'available' => $has_package_access && $has_required_config,
            'region' => $fine_tunnel->address ?? '',
            'courier_codes' => $fine_tunnel->couriers->pluck('code')->unique()->implode(','),
            'weight' => (int)$fine_tunnel->weight,
        ];
    }

    private function createShippingUnavailableReply($history, int $follow_ups_count): array
    {
        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $follow_ups_count > 0 ? 'no' : 'yes',
            'message' => "Maaf kak 🙏, aku nggak bisa bantu cek ongkirnya langsung nih. Tapi kalau mau, aku bisa kasih cara atau link biar kakak bisa cek sendiri 😊",
        ]);

        return [
            'reply' => $reply,
            'message' => ['text' => $reply->message]
        ];
    }

    private function processShippingRequest($intent, array $shipping_config, $fine_tunnel, $context): string
    {
        $pos_code = $intent->pos_code ?? null;
        $quantity = $intent->quantity ?? 1;
        $address = $intent->address ?? '';
        $message = $intent->message ?? null;

        if (empty($pos_code)) {
            return $this->cleanText($message);
        }

        $api_key = $this->getShippingApiKey($context);
        $api_method = $this->generalSetting->ongkir_method;

        return $this->calculateShippingCost(
            $api_method,
            $api_key,
            $fine_tunnel,
            $pos_code,
            $quantity,
            $address,
            $shipping_config
        );
    }

    private function getShippingApiKey(array $shipping_config): string
    {
        return $this->generalSetting->cek_ongkir_option_api == 'sistem'
            ? $this->generalSetting->cek_ongkir_api
            : $shipping_config['business_setting']->cek_ongkir_api ?? '';
    }

    private function calculateShippingCost(string $api_method, string $api_key, $fine_tunnel, string $pos_code, int $quantity, string $address, array $shipping_config): string
    {
        if ($api_method == 'rajaongkir') {
            return $this->calculateRajaOngkirCost($api_key, $fine_tunnel, $pos_code, $shipping_config);
        }

        if ($api_method == 'biteship') {
            return $this->calculateBiteshipCost($api_key, $fine_tunnel, $pos_code, $quantity, $address, $shipping_config);
        }

        return 'Metode pengiriman tidak tersedia.';
    }

    private function calculateRajaOngkirCost(string $api_key, $fine_tunnel, string $pos_code, array $shipping_config): string
    {
        $response = $this->rajaOngkirObserver->checkOngkir(
            $api_key,
            $fine_tunnel->zip_code,
            $pos_code,
            $shipping_config['weight'],
            $shipping_config['courier_codes']
        );

        if ($response->status() != 200) {
            return 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        $ongkir_data = json_decode($response->body(), true);
        $message = '';

        foreach ($ongkir_data['data'] as $ongkir) {
            $price = number_format($ongkir['cost'], 0, ',', '.');
            $message .= "- {$ongkir['name']} - {$ongkir['service']} ({$ongkir['etd']} hari): Rp {$price}\n";
        }

        return $message;
    }

    private function calculateBiteshipCost(string $api_key, $fine_tunnel, string $pos_code, int $quantity, string $address, array $shipping_config): string
    {
        $response = $this->biteshipServiceObserver->checkOngkir(
            $api_key,
            $fine_tunnel->zip_code,
            $pos_code,
            $shipping_config['weight'],
            $shipping_config['courier_codes'],
            $quantity
        );

        if ($response->status() != 200) {
            return 'Terjadi kesalahan, saat ini kami tidak dapat melakukan pengecekan ongkos kirim.';
        }

        $ongkir_data = json_decode($response->body(), true);

        return $this->formatBiteshipResponse($ongkir_data, $quantity, $address);
    }

    private function formatBiteshipResponse(array $ongkir_data, int $quantity, string $address): string
    {
        $origin = $ongkir_data['origin']['administrative_division_level_4_name'] . ', ' .
            $ongkir_data['origin']['administrative_division_level_3_name'] . ', ' .
            $ongkir_data['origin']['administrative_division_level_2_name'];

        $message = "📦 Detail Pengecekan Ongkos Kirim\n";
        $message .= "Jumlah barang: *{$quantity}*\n";
        $message .= "Alamat tujuan: *{$address}*\n\n";
        $message .= "🚚 Pengiriman\n";
        $message .= "Dari: *{$origin}*\n";
        $message .= "Ke: *{$address}*\n";
        $message .= "Berikut detail ongkirnya:\n\n";

        foreach ($ongkir_data['pricing'] as $ongkir) {
            $duration = $ongkir['duration'];
            $courier = $ongkir['courier_name'];
            $service = $ongkir['courier_service_name'];
            $price = number_format($ongkir['price'], 0, ',', '.');

            $message .= "- $courier - $service ($duration): Rp $price\n";
        }

        return $message;
    }

    private function determineMessageTypeFromMime(string $mime_type): string
    {
        foreach ($this->typeMimeData as $type => $mime_types) {
            if (in_array($mime_type, $mime_types)) {
                return $type;
            }
        }
        return 'text';
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

    public function triggerEmit($replyForEmit, $userForEmit, $welcomeForEmit)
    {
        $expressUrl = config('services.express.url') . '/trigger-whatsapp';

        if ($welcomeForEmit) {
            Http::post($expressUrl, MessagestResource::make($welcomeForEmit));
        }

        if ($userForEmit) {
            // Kirim request pertama
            $response = Http::post($expressUrl, MessagestResource::make($userForEmit));

            // Jika request pertama berhasil, baru kirim request kedua
            if ($response->successful() && $replyForEmit) {
                Http::post($expressUrl, MessagestResource::make($replyForEmit));
            }
        } elseif ($replyForEmit) {
            // Jika $userForEmit tidak ada, langsung kirim request kedua
            Http::post($expressUrl, MessagestResource::make($replyForEmit));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Validation For Auto Reply Days and Time
    |--------------------------------------------------------------------------
    */

    public function checkingTimeAutoReply(WhatsappDevice $device)
    {

        /**
         * Check For Activation Days 
         */

        if ($device->auto_reply_certain_day == 'yes') {
            if ($device->days != null) {
                $day        = date("D");
                $getVoucher = WhatsappDevice::where("id", $device->id)->whereRaw("find_in_set('" .  $day . "',days)")->count(); // Check Day in This Auto Reply

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
    | 3. Whatsapp Message For Use Auto Reply
    |--------------------------------------------------------------------------
    */

    public function autoReplyMessage(WhatsappDevice $device, $message, $name = '', $from, $type = 'personal')
    {
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $device->id . "',select_device)")->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        if ($chatBot->reply_method == 'text') {
            $messageText = str_replace('{name}', $name, $chatBot->message);

            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'image'         => null,
                'file_type'     => null,
                'file_size'     => null,
                'message'       => array(
                    'text'          => $messageText
                )
            );
        }

        if ($chatBot->reply_method == 'template' && $chatBot->template) {
            $file = '';
            $fileType = null;
            $fileSize = 0;

            if ($chatBot->template->image && file_exists($chatBot->template->image)) {
                $file = asset($chatBot->template->image);
                $fileType = mime_content_type($chatBot->template->image); // atau hardcode 'image/jpeg'
                $fileSize = filesize($chatBot->template->image);
            }

            $messageText    = $chatBot->template->message ?? '';
            $messageText    = str_replace('{name}', $name, $messageText);
            $messageData    = $this->whatsappServiceObserver->formatDataMessage($messageText, $file, $chatBot->template->type_content, json_decode($chatBot->template->button_or_list, true));

            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'image'         => $file,
                'file_type'     => $fileType,
                'file_size'     => $fileSize,
                'message'       => $messageData
            );
        }

        if ($chatBot->reply_method == 'image') {
            foreach ($chatBot->details as $detail) {
                $this->whatsappServiceObserver->sendMessage($from, $device->id, $detail->name ?? '', $detail->url, 'description', null, ($type == 'personal' ? false : true));
            }

            return array(
                'status'    => true,
                'message_text'  => null,
                'image'         => null,
                'file_type'     => null,
                'file_size'     => null,
                'message'   => array(
                    'text'      => ''
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Any Auto Reply
    |--------------------------------------------------------------------------
    */

    public function anyAutoReply(WhatsappDevice $device, $name)
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

        if ($device->reply_method == 'template') {

            $file           = $device->template->image != null ? asset($device->template->image) : '';
            $messageText    = $device->template->text ?? '';
            $messageData    = $this->whatsappServiceObserver->formatDataMessage($messageText, $file, $device->template->type_content, json_decode($device->template->button_or_list, true));
            return array(
                'status'        => true,
                'message_text'  => $messageText,
                'message'       => $messageData
            );
        }
    }

    public function sendCustomWebHook(Request $request, WhatsappDevice $device)
    {

        $request_from   = explode('@', $request->from);
        $request_from   = $request_from[0];

        return Http::accept('application/json')->post($device->webhook, [
            'device_key'    => $device->id,
            'name'          => $request->from_name,
            'from'          => $request_from,
            'message'       => $request->message,
            'type'          => $request->type
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

    /*
    |--------------------------------------------------------------------------
    | 6. Callback Me
    |--------------------------------------------------------------------------
    */

    public function callbackMe(Request $request, $device_id)
    {

        $device_id      = str_replace('device_', '', $device_id);
        $device         = WhatsappDevice::find($device_id);
        $request_from   = explode('@', $request->from);
        $fromType       = $request_from[1] == 'g.us' ? 'group' : 'personal';

        $message        = $request->message ?? null;
        $replyForEmit   = null;
        $userForEmit    = null;
        $messageType    = 'text';
        $image  = array(
            'status'    => false,
            'type'      => null,
            'size'      => null,
            'path'      => null
        );

        try {

            DB::beginTransaction();

            $history      = $this->historyChatObserver->getByNumber($fromType, $request_from[0], $device->id);

            if ($history) {
                if ($history->status != 'block') {
                    if ($history->status != 'open') {
                        $history->update([
                            'status'    => 'open'
                        ]);
                    }
                }

                if ($request->hasFile('file')) {

                    $file       = $request->file('file');
                    $fileType   = $file->getMimeType();
                    $fileSize   = $file->getSize();

                    $storageCheck = $this->checkStorage($history->business, $fileSize);

                    if (!$storageCheck['available']) {
                        $imagePath = 'images/user.png';
                        $messageType = 'image';

                        $image = [
                            'status'    => true,
                            'type'      => 'image/png',
                            'size'      => 0,
                            'path'      => $imagePath,
                        ];
                    } else {

                        $messageType = 'file';

                        if ($fileType) {
                            foreach ($this->typeMimeData as $key => $mimeTypes) {
                                if (in_array($fileType, $mimeTypes)) {
                                    $messageType = $key;
                                    break;
                                }
                            }
                        }

                        $subFolder  = $this->getSubFolderByMimeType($fileType);
                        $uploadPath = 'folders/' . $history->business_id . '/' . $subFolder . '/';
                        $this->ensureDirectoryExists($uploadPath);
                        $imagePath  = $this->uploadImage($request, 'file', $uploadPath);

                        $image = [
                            'status'    => $imagePath != null,
                            'type'      => $fileType,
                            'size'      => $fileSize,
                            'path'      => $imagePath,
                        ];
                    }
                }

                $replyForChat = null;
                if (isset($request->stanzaId) && $request->stanzaId != null) {
                    $replyForChat = HistoryChatDetail::where('messageid', $request->stanzaId)->first();
                }

                $userForEmit = $history->details()->create([
                    'file_path'             => $image['path'],
                    'file_type'             => $image['type'],
                    'file_size'             => $image['size'],
                    'type'                  => $messageType,
                    'history_chat_id'       => $history->id,
                    'from'                  => 'device',
                    'is_read'               => 'yes',
                    'message'               => $message,
                    'remotejid'             => $request->from,
                    'messageid'             => $request->message_id,
                    'reply_to'              => $replyForChat->id ?? null,
                    'reply_text'            => $replyForChat->message ?? null,
                    'quoted_message'        => $request->quoted_message,
                ]);

                if ($history->takeover == 'no') {
                    $history->update([
                        'takeover'      => 'yes'
                    ]);
                }
            }

            DB::commit();

            $this->triggerEmit($replyForEmit, $userForEmit, null);

            return response()->json([
                'message'       => array('text' => null),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Send Message To Agent
    |--------------------------------------------------------------------------
    */

    public function sendToAgent(HistoryChat $history, $incomingMessage, User $user, WhatsappDevice $device)
    {
        $lines = [
            "👋 *Halo Agent {$user->name}*,",
            "",
            "Kami menerima pesan dari pelanggan:",
            "*{$history->name}*",
            "",
            "📩 *Pesan:*",
            $incomingMessage,
            "",
            "Mohon segera ditanggapi. Terima kasih 🙏"
        ];

        $formattedMessage = implode(PHP_EOL, $lines);

        $this->whatsappServiceObserver->sendMessage(
            $user->phone,
            $device->id,
            $formattedMessage,
            '',
            'description',
            null,
            false,
            null
        );
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
