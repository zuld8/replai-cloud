<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveChat\HistoryChatResources;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\HistoryChat;
use App\Models\InternalSetting;
use App\Models\LiveChat;
use App\Models\Master\Label;
use App\Models\Setting;
use App\Observers\ChatBot\BiteshipServiceObserver;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\HistoryChatObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\ChatBot\RajaOngkirServiceObserver;
use App\Observers\LiveChatObserver;
use App\Observers\Store\StoreObserver;
use App\Supports\MimeTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LiveChatController extends Controller
{
    protected $livechatObserver;
    protected $historyChatObserver;
    protected $openAiServiceObserver;
    protected $geminiAiServiceObserver;
    protected $storeObserver;
    protected $rajaOngkirObserver;
    protected $biteshipServiceObserver;
    protected $generalSetting;
    protected $typeMimeData;

    public function __construct(
        LiveChatObserver $liveChatObserver,
        HistoryChatObserver $historyChatObserver,
        OpenAiServiceObserver $openAiServiceObserver,
        GeminiAiServiceObserver $geminiAiServiceObserver,
        StoreObserver $storeObserver,
        RajaOngkirServiceObserver $rajaOngkirObserver,
        BiteshipServiceObserver $biteshipServiceObserver
    ) {
        $this->livechatObserver         = $liveChatObserver;
        $this->historyChatObserver      = $historyChatObserver;
        $this->openAiServiceObserver    = $openAiServiceObserver;
        $this->geminiAiServiceObserver  = $geminiAiServiceObserver;
        $this->storeObserver            = $storeObserver;
        $this->rajaOngkirObserver       = $rajaOngkirObserver;
        $this->biteshipServiceObserver  = $biteshipServiceObserver;
        $this->generalSetting           = Setting::where('merchant_id', null)->first(['open_ai_key', 'ai_option', 'cek_ongkir_option_api', 'cek_ongkir_api', 'ongkir_method']);
        $this->typeMimeData             = MimeTypes::TYPE_MAP;
    }

    public function getInformation(Request $request)
    {
        $this->validate($request, [
            'token'         => 'required'
        ]);

        $livechat   = $this->livechatObserver->getById($request->token);

        return response()->json([
            'token'         => $livechat->id,
            'name'          => $livechat->name,
            'description'   => $livechat->description,
            'img_url'       => asset($livechat->image_data),
            'detail'        => array(
                'welcome_mgs'   => $livechat->finetunnel->welcome_message ?? null,
                'faq'           => $livechat->faqs != null ? json_decode($livechat->faqs, true) : null,
                'links'         => $livechat->sosmed != null ? json_decode($livechat->sosmed, true) : array()
            ),
        ], 200);
    }

    public function checkForChat(Request $request, LiveChat $chat)
    {
        $this->validate($request, [
            'phone'         => 'required'
        ]);

        try {
            DB::beginTransaction();

            $histories      = $this->historyChatObserver->getByNumber('personal', $request->phone, null, 'livechat', $chat->id);

            if (!$histories) {
                $settings       = Setting::where('id', $chat->business_id)->first(['history_ai_chat_option', 'history_ai_chat']);
                $expireDate     = $settings->history_ai_chat > 0 ? now()->addDays($settings->history_ai_chat)->format('Y-m-d')  : now()->format('Y-m-d');
                $histories      = $this->historyChatObserver->createData(null, 'personal', $request->phone, $expireDate, $chat->merchant_id, $chat->business_id, $request->name, 'livechat', $chat->id);

                if ($chat->finetunnel && $chat->finetunnel->welcome_message) {
                    $this->createWelcomeMessage($chat, $histories);
                }
            }

            $stores         = $this->storeObserver->checkByNumber($histories->from_number, $chat->business_id);
            if (!$stores) {
                $store =  $this->storeObserver->createByHistory($histories);
            }

            if (empty($histories->store_id)) {
                $histories->update(['store_id'    => $store->id]);
            }

            DB::commit();

            return response()->json([
                'id'            => $histories->id,
                'name'          => $histories->name,
                'phone'         => $histories->from_number
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function chatHistories(Request $request, HistoryChat $history)
    {
        return response()->json([
            'data'      => HistoryChatResources::collection($history->details_desc),
            'metadata'  => array(
                'limit'         => 1000,
                'page'          => 1,
                'total'         => (int)$history->details()->count(),
                'totalPages'    => 1
            ),
        ], 200);
    }

    public function sendMessage(Request $request, HistoryChat $history)
    {
        if ($history->status == 'block') {
            return;
        }

        if ($history->status != 'open') {
            $history->update(['status' => 'open']);
        }

        // Mark previous messages as follow up
        $history->details()
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->update([
                'is_follow_up' => 'yes',
                'follow_up_id' => null
            ]);

        $image = $this->processFileUpload($request, $history);

        $message = $this->historyChatObserver->sendMessage($history, $request->message, 'user', $image);
        return response()->json(HistoryChatResources::make($message), 200);
    }

    public function callbackMessage(Request $request, HistoryChat $history)
    {
        if (!$request->message) {
            return;
        }

        $livechat       = $history->livechat;
        $setting        = Setting::where('id', $history->business_id)->first(['is_online', 'id', 'cek_ongkir_api']);
        $followUps      = $livechat->finetunnel ? $livechat->finetunnel->follow_ups->count() : 0;
        $message        = $request->message;
        $historyMessage = null;

        try {
            DB::beginTransaction();

            // Process labels first
            $this->processLabels($livechat, $history, $message, $setting);

            // Try chatbot first
            if (in_array($livechat->type, ['chatbot', 'all']) && $history->takeover == 'no' && $history->status != 'block') {
                $chatbot_reply = $this->processChatbotReply($livechat, $history, $message, $followUps);
                if ($chatbot_reply) {
                    $historyMessage = $chatbot_reply;
                }
            }

            // Try AI if chatbot didn't reply
            if (!$historyMessage && in_array($livechat->type, ['ai', 'all']) && $this->canProcessAI($history, $setting, $livechat)) {
                $ai_reply = $this->processAIReply($livechat, $history, $message, $setting, $followUps);
                if ($ai_reply) {
                    $historyMessage = $ai_reply;
                }
            }

            DB::commit();

            if ($historyMessage) {
                return response()->json(HistoryChatResources::make($historyMessage), 200);
            }
        } catch (\Exception $e) {
            DB::rollBack(); 
        }
    }

    private function createWelcomeMessage($chat, $histories): void
    {
        $messageType = 'text';
        $image = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        if (file_exists($chat->finetunnel->welcome_image)) {
            $filePath = public_path($chat->finetunnel->welcome_image);
            $fileType = mime_content_type($filePath);
            $fileSize = filesize($filePath);

            $image = [
                'status' => true,
                'type' => $fileType,
                'size' => $fileSize,
                'path' => $chat->finetunnel->welcome_image
            ];

            $messageType = $this->determineMessageTypeFromMime($fileType);
        }

        $welcomeMessage = str_replace('{name}', $histories->name ?? '', $chat->finetunnel->welcome_message);

        $histories->details()->create([
            'file_path'      => $image['path'],
            'file_type'      => $image['type'],
            'file_size'      => $image['size'],
            'history_chat_id' => $histories->id,
            'type'           => $messageType,
            'from'           => 'history',
            'message'        => $welcomeMessage,
        ]);
    }

    private function processFileUpload(Request $request, $history): array
    {
        $image = [
            'status' => false,
            'type' => null,
            'size' => null,
            'path' => null
        ];

        if (!$request->hasFile('file')) {
            return $image;
        }

        $file = $request->file('file');
        $fileType = $file->getMimeType();
        $fileSize = $file->getSize();

        $storageCheck = $this->checkStorage($history->business, $fileSize);

        if (!$storageCheck['available']) {
            return [
                'status' => true,
                'type' => 'image/png',
                'size' => 0,
                'path' => 'images/user.png',
            ];
        }

        $subFolder = $this->getSubFolderByMimeType($fileType);
        $uploadPath = 'folders/' . $history->business_id . '/' . $subFolder . '/';
        $this->ensureDirectoryExists($uploadPath);
        $imagePath = $this->uploadImage($request, 'file', $uploadPath);

        return [
            'status' => $imagePath != null,
            'type' => $fileType,
            'size' => $fileSize,
            'path' => $imagePath,
        ];
    }

    private function processLabels($livechat, $history, string $message, $setting): void
    {
        $fineTunnel = $livechat->finetunnel;
        if (!$fineTunnel || !$fineTunnel->label) {
            return;
        }

        $labelIds = array_map('trim', explode(',', $fineTunnel->label));

        $matchingLabel = Label::where('business_id', $setting->id)
            ->where('type', 'keyword')
            ->whereIn('id', $labelIds)
            ->whereRaw("? REGEXP REPLACE(tag, ', ', '|')", [$message])
            ->first(['id', 'name']);

        if (!$matchingLabel || !$history) {
            return;
        }

        $currentLabels = json_decode($history->label, true) ?? [];
        $alreadyExists = collect($currentLabels)->contains('id', $matchingLabel->id);

        if (!$alreadyExists) {
            $currentLabels[] = [
                'id' => $matchingLabel->id,
                'name' => $matchingLabel->name,
            ];

            $history->update(['label' => json_encode($currentLabels)]);

            if ($history->store && $history->store->label_id == null) {
                $history->store->update(['label_id' => $matchingLabel->id]);
            }
        }
    }

    private function processChatbotReply($livechat, $history, string $message, int $followUps)
    {
        $reply = $this->autoReplyMessage($livechat, $message, $history->name ?? '');

        if (!$reply['status']) {
            return null;
        }

        return $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'file_path' => $reply['image'] ?? null,
            'file_type' => $reply['file_type'] ?? null,
            'file_size' => $reply['file_size'] ?? null,
            'type' => $reply['image'] ? 'image' : 'text',
            'message' => $reply['message']
        ]);
    }

    private function processAIReply($livechat, $history, string $message, $setting, int $followUps)
    {
        $fineTunnel = $livechat->finetunnel;

        if (!$this->hasValidCredits($livechat)) {
            return null;
        }

        // Detect intent
        $intent_data = $this->detectIntent($livechat, $history, $message);

        if (!$intent_data['success']) {
            return null;
        }

        $intent = $intent_data['intent'];
        $credit_used = $intent_data['credit_used'];

        // Handle different intents
        $reply_result = $this->handleIntent($intent, $history, $livechat, $setting, $followUps);

        if ($reply_result['reply']) {
            $this->updateCredits($credit_used, $livechat, $fineTunnel, $reply_result['reply']);
        }

        return $reply_result['reply'];
    }

    private function detectIntent($livechat, $history, string $message): array
    {
        $fineTunnel = $livechat->finetunnel;
        $conversations = $history->details_desc
            ->take($fineTunnel->history_limit)
            ->sortBy('created_at');

        $intent_response = $this->openAiServiceObserver->detectIntent(
            $fineTunnel,
            $this->generalSetting->open_ai_key,
            $message,
            $conversations,
            $fineTunnel->model_ai,
            null
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

    private function handleIntent($intent, $history, $livechat, $setting, int $followUps): array
    {
        // Normalize intent structure
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
                return $this->handleMediaIntent($intent, $history, $followUps);

            case 'check_shipping':
                return $this->handleCheckShippingIntent($intent, $history, $livechat, $setting, $followUps);

            case 'question':
                return $this->handleQuestionIntent($intent, $history, $followUps);

            default:
                return ['reply' => null];
        }
    }

    private function handleQuestionIntent($intent, $history, int $followUps): array
    {
        $reply_message = $this->cleanText($intent->message);

        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => $reply_message
        ]);

        return ['reply' => $reply];
    }

    private function handleMediaIntent($intent, $history, int $followUps): array
    {
        $media_urls = $intent->medias ?? [];
        $message = $intent->message ?? '';
        $reply_message = null;

        if (count($media_urls) > 0) {
            foreach ($media_urls as $media_url) {
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
                        'type' => 'text',
                        'is_follow_up' => 'yes',
                        'message' => $message,
                        'credit_using' => 0,
                    ]);
                }
            }

            return ['reply' => $reply_message];
        } else {
            $reply_message = $this->cleanText($intent->message);

            $reply = $history->details()->create([
                'history_chat_id' => $history->id,
                'from' => 'device',
                'is_read' => 'yes',
                'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
                'message' => $reply_message
            ]);

            return ['reply' => $reply];
        }
    }

    private function handleCheckShippingIntent($intent, $history, $livechat, $setting, int $followUps): array
    {
        $fineTunnel = $livechat->finetunnel;
        $shipping_config = $this->getShippingConfig($fineTunnel, $setting);

        if (!$shipping_config['available']) {
            return $this->createShippingUnavailableReply($history, $followUps);
        }

        $reply_message = $this->processShippingRequest($intent, $shipping_config, $fineTunnel, $setting);

        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => $reply_message,
        ]);

        return ['reply' => $reply];
    }

    private function getShippingConfig($fineTunnel, $setting): array
    {
        $has_package_access = ($setting->package_active &&
            $setting->package_active->cek_ongkir == 'yes') ||
            is_null($setting->merchant_id);

        $has_required_config = $fineTunnel->couriers->count() > 0 &&
            !empty($fineTunnel->zip_code) &&
            (int)$fineTunnel->weight > 0;

        return [
            'available' => $has_package_access && $has_required_config,
            'region' => $fineTunnel->address ?? '',
            'courier_codes' => $fineTunnel->couriers->pluck('code')->unique()->implode(','),
            'weight' => (int)$fineTunnel->weight,
        ];
    }

    private function createShippingUnavailableReply($history, int $followUps): array
    {
        $reply = $history->details()->create([
            'history_chat_id' => $history->id,
            'from' => 'device',
            'is_read' => 'yes',
            'is_follow_up' => $followUps > 0 ? 'no' : 'yes',
            'message' => "Maaf kak 🙏, aku nggak bisa bantu cek ongkirnya langsung nih. Tapi kalau mau, aku bisa kasih cara atau link biar kakak bisa cek sendiri 😊",
        ]);

        return ['reply' => $reply];
    }

    private function processShippingRequest($intent, array $shipping_config, $fineTunnel, $setting): string
    {
        $pos_code = $intent->pos_code ?? null;
        $quantity = $intent->quantity ?? 1;
        $address = $intent->address ?? '';
        $message = $intent->message ?? null;

        if (empty($pos_code)) {
            return $this->cleanText($message);
        }

        $api_key = $this->getShippingApiKey($setting);
        $api_method = $this->generalSetting->ongkir_method;

        return $this->calculateShippingCost(
            $api_method,
            $api_key,
            $fineTunnel,
            $pos_code,
            $quantity,
            $address,
            $shipping_config
        );
    }

    private function getShippingApiKey($setting): string
    {
        return $this->generalSetting->cek_ongkir_option_api == 'sistem'
            ? $this->generalSetting->cek_ongkir_api
            : $setting->cek_ongkir_api ?? '';
    }

    private function calculateShippingCost(string $api_method, string $api_key, $fineTunnel, string $pos_code, int $quantity, string $address, array $shipping_config): string
    {
        if ($api_method == 'rajaongkir') {
            return $this->calculateRajaOngkirCost($api_key, $fineTunnel, $pos_code, $shipping_config);
        }

        if ($api_method == 'biteship') {
            return $this->calculateBiteshipCost($api_key, $fineTunnel, $pos_code, $quantity, $address, $shipping_config);
        }

        return 'Metode pengiriman tidak tersedia.';
    }

    private function calculateRajaOngkirCost(string $api_key, $fineTunnel, string $pos_code, array $shipping_config): string
    {
        $response = $this->rajaOngkirObserver->checkOngkir(
            $api_key,
            $fineTunnel->zip_code,
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

    private function calculateBiteshipCost(string $api_key, $fineTunnel, string $pos_code, int $quantity, string $address, array $shipping_config): string
    {
        $response = $this->biteshipServiceObserver->checkOngkir(
            $api_key,
            $fineTunnel->zip_code,
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

    private function canProcessAI($history, $setting, $livechat): bool
    {
        return trim(strtolower($history->takeover)) === 'no' &&
            $history->status != 'block' &&
            $setting &&
            $setting->is_online === 'yes' &&
            $livechat->finetunnel;
    }

    private function hasValidCredits($livechat): bool
    {
        if (is_null($livechat->business->merchant ?? null)) {
            return true;
        }

        $topupLimit = $livechat->business->package_active_topup->sisa_credit ?? 0;
        $packageCredit = $livechat->business->package_active->sisa_credit ?? 0;

        return $topupLimit > 0 || $packageCredit > 0;
    }

    private function updateCredits(int $credit_used, $livechat, $fineTunnel, $reply_message): void
    {
        if ($credit_used <= 0 || is_null($livechat->business->merchant ?? null)) {
            return;
        }

        $setting = InternalSetting::first(['credit_token_basic', 'credit_token_advance']);
        $total_credit_using = $this->calculateCreditUsage($credit_used, $fineTunnel->model_ai, $setting);

        $this->deductCredits($livechat, $total_credit_using);

        if ($reply_message) {
            $reply_message->update(['credit_using' => $total_credit_using]);
        }
    }

    private function calculateCreditUsage(int $credit_used, string $model_ai, $setting): int
    {
        $credit_per_250_tokens = 1;

        $tokens_per_credit = $model_ai == 'advanced'
            ? $setting->credit_token_advance
            : $setting->credit_token_basic;

        return ceil($credit_used / $tokens_per_credit) * $credit_per_250_tokens;
    }

    private function deductCredits($livechat, int $total_credit_using): void
    {
        $package_credit = $livechat->business->package_active->sisa_credit ?? 0;

        if ($package_credit > 0) {
            $livechat->business->package_active->update([
                'using_credit_limit' => $livechat->business->package_active->using_credit_limit + $total_credit_using
            ]);
        } else {
            $livechat->business->package_active_topup->update([
                'using_credit_limit' => $livechat->business->package_active_topup->using_credit_limit + $total_credit_using
            ]);
        }
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

    private function cleanText($text)
    { 
        $text = preg_replace('/([^\(]+)\((https?:\/\/[^\s\)]+)\)/', '$1 $2', $text);
 
        $text = preg_replace('/(https?:\/\/[^\s\]]+)\]/', '$1', $text);
 
        preg_match_all('/https?:\/\/[^\s]+/', $text, $matches);
        $urls = $matches[0];
        foreach ($urls as $index => $url) {
            $text = str_replace($url, "__URL{$index}__", $text);
        }
 
        $text = str_replace('**', '*', $text);
 
        $text = preg_replace('/[\#\!\{\}\[\]\(\)]/', '', $text);
 
        $text = preg_replace('/(?<=\s)[^\p{L}\p{N}-](?=\s)/u', '', $text);
 
        $text = preg_replace_callback('/^.*$/m', function ($lineMatches) {
            $line = $lineMatches[0];

            return preg_replace_callback('/\S+/', function ($matches) use ($line) {
                $word = $matches[0];

                if (preg_match('/^__URL\d+__$/', $word)) return $word;
                if (trim($line)[0] === '-' && $word === '-') return $word;

                if (!preg_match('/^@[\p{L}\p{N}_-]+$/u', $word)) {
                    $word = str_replace('@', '', $word);
                }

                if (strpos($word, '?') !== false && substr($word, -1) !== '?') {
                    $word = str_replace('?', '', $word);
                }
                if (strpos($word, ':') !== false && substr($word, -1) !== ':') {
                    $word = str_replace(':', '', $word);
                }

                if (preg_match('/^\d+\s?%$/', $word)) return $word;
                if (preg_match('/^[$£€¥Rp]+\s?\d+([\.,]\d+)*$/', $word)) return $word;

                return preg_replace(
                    '/[^\p{L}\p{N}\.\,\;\_\-\?\:\%\/\$\@\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u',
                    '',
                    $word
                );
            }, $line);
        }, $text);
 
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

    public function autoReplyMessage(LiveChat $chat, $message, $name = '')
    {
        $chatBot = ChatBot::whereRaw("find_in_set('" .  $chat->id . "',select_livechat)")->with('template')
            ->whereRaw("? REGEXP REPLACE(keyword, ', ', '|')", [$message])->first();

        if (!$chatBot) {
            return array(
                'status'    => false,
                'message'   => null
            );
        }

        $messageText = str_replace('{name}', $name, $chatBot->message);

        if ($chatBot->reply_method == 'text') {
            return array(
                'status'    => true,
                'message'   => $messageText
            );
        }

        if ($chatBot->reply_method == 'template') {
            $messageText = str_replace('{name}', $name, $chatBot->template->message ?? '');

            $file = null;
            $fileType = null;
            $fileSize = 0;

            if ($chatBot->template->image && file_exists($chatBot->template->image)) {
                $file = asset($chatBot->template->image);
                $fileType = mime_content_type($chatBot->template->image);
                $fileSize = filesize($chatBot->template->image);
            }

            return array(
                'status'     => true,
                'image'      => $file,
                'file_type'  => $fileType,
                'file_size'  => $fileSize,
                'message'    => $messageText
            );
        }

        return array(
            'status'    => false,
            'message'   => null
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

            $usedStorageMB  = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB     = round($fileSize / 1024 / 1024, 2);

            $storageFromSubscribe   = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons      = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage           = $storageFromSubscribe + $storageFromAddons;

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
        $mainType = explode('/', $mimeType)[0];

        switch ($mainType) {
            case 'image':
                return 'livechat-images';
            case 'video':
                return 'livechat-video';
            case 'audio':
                return 'livechat-audio';
            case 'application':
            case 'text':
            default:
                return 'livechat-document';
        }
    }
}