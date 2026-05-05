<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\Official\SendWabaWithTemplateRequest;
use App\Models\ChatBot\HistoryChat;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\Store\Store;
use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use App\Models\WhatsappKeyAccount;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Http\Resources\LiveChat\HistoryChatResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WabaMessageController extends Controller
{

    protected $whatsappOfficialServiceObserver;

    public function __construct(WhatsappOfficialServiceObserver $whatsappOfficialServiceObserver)
    {
        $this->whatsappOfficialServiceObserver      = $whatsappOfficialServiceObserver;
    }

    public function sendMessage(Request $request)
    {

        $settings       = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id', 'api_device_use', 'whatsapp_sender_notif']);
        $messageType    = 'description';

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Cannot be recognized'
            ], 401);
        }

        $device = null;

        if ($settings->api_device_use == 'required') {
            $device     = WhatsappKeyAccount::where("business_id", $settings->id)->where("id", $request->device_key)->first();
        } else {
            $devices     = WhatsappKeyAccount::where(function ($q) {
                return $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no");
            })->where("business_id", $settings->id)->where("status", "active");

            if ($settings->whatsapp_sender_notif === 'sequence' && $devices->count() > 0) {
                $device                                = $devices->first();
            } elseif ($settings->whatsapp_sender_notif === 'spin' && $devices->count() > 0) {
                $deviceData                            = collect($devices->get())->shift();
                $device                                = $devices->where("id", $deviceData->id)->first();
            } elseif ($settings->whatsapp_sender_notif === 'random' && $devices->count() > 0) {
                $deviceData                            = collect($devices->get())->random();
                $device                                = $devices->where("id", $deviceData->id)->first();
            }
        }


        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device tidak di temukan'
            ], 401);
        }

        $config                                 = $device->meta_data;
        $config                                 = $config ? json_decode($config, true) : [];
        $messageVariable['appid']               = $config['whatsapp']['app_id'] ?? null;
        $messageVariable['phoneid']             = $config['whatsapp']['phone_number_id'] ?? null;
        $messageVariable['wabaid']              = $config['whatsapp']['waba_id'] ?? null;
        $messageVariable['access_token']        = $config['whatsapp']['access_token'] ?? null;

        if ($request->url) {

            $fileUrl        = $request->url;
            $extension      = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            $mimeType       = $this->getMimeTypeFromExtension($extension);
            $messageType    = $this->getWhatsAppMediaType($mimeType);
            $sendMedia      = $this->whatsappOfficialServiceObserver->uploadMedia($messageVariable['access_token'], $request->url, $messageType, $messageVariable['phoneid']);

            if ($sendMedia != null) {
                $sendMessage    = $this->whatsappOfficialServiceObserver->sendMediaMessage($request->phone, $messageType, $sendMedia, $request->message, $request->url, $messageVariable);
                return response()->json([
                    'status'    => $sendMessage['status'] == 200 ? true : false,
                    'message'   => $sendMessage['message']
                ], $sendMessage['status']);
            }
        } else {

            $sendMessage    = $this->whatsappOfficialServiceObserver->sendTextMessage($request->phone, $request->message, $messageVariable);

            // Record to CRM if successful
            if (($sendMessage['status'] ?? 500) == 200) {
                try {
                    $this->recordSimpleMessageToCrm($device, $this->normalizePhoneNumber($request->phone), $request->message, $sendMessage);
                } catch (\Exception $crmEx) {
                    Log::warning('CRM record failed for simple WABA msg: ' . $crmEx->getMessage());
                }
            }

            return response()->json([
                'status'    => $sendMessage['status'] == 200 ? true : false,
                'message'   => $sendMessage['message']
            ], $sendMessage['status']);
        }
    }

    /**
     * Send WhatsApp message using template
     * 
     * @param WabaSendTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTemplateMessage(SendWabaWithTemplateRequest $request)
    {
        try {
            // 1. Validate API Key & Get Settings
            $settings = Setting::where('local_api_key', $request->api_key)
                ->first(['id', 'api_device_use', 'whatsapp_sender_notif']);

            if (!$settings) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'API Key tidak valid'
                ], 401);
            }

            // 2. Get Device
            $device = $this->getAvailableDevice($settings, $request->device_key);

            if (!$device) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Device tidak tersedia atau sudah mencapai limit'
                ], 400);
            }


            $template = MessageTemplate::where('meta_account_id', $device->meta_account_id)
                ->where('id', $request->template_id)
                ->where('lang', $request->template_lang)
                ->where('waba_status_template', 'APPROVED')
                ->first();

            if (!$template) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Template tidak ditemukan atau belum approved'
                ], 404);
            }

            // 4. Extract Device Config
            $config = $device->meta_data ? json_decode($device->meta_data, true) : [];

            $messageVariable = [
                'appid'         => $device->meta->app_id ?? null,
                'phoneid'       => $config['whatsapp']['phone_number_id'] ?? null,
                'wabaid'        => $device->meta->business_app ?? null,
                'access_token'  => $device->meta->access_token ?? null,
            ];

            // Validate config completeness
            if (!$messageVariable['phoneid'] || !$messageVariable['access_token']) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Konfigurasi device tidak lengkap'
                ], 500);
            }

            // 5. Normalize Phone Number
            $phone = $this->normalizePhoneNumber($request->phone);

            // 3. Get Template
            $templateName = strtolower(preg_replace("/[^0-9a-zA-Z]/", "_", $template->name));

            // 6. Build Template Structure
            $templateStructure = $this->buildTemplateStructure(
                $templateName,
                $request->template_lang,
                $request->header,
                $request->body,
                $request->buttons
            );

            // 7. Send Message
            $result = $this->sendTemplateMessageViaApi(
                $phone,
                $templateStructure,
                $messageVariable
            );

            // Log for debugging
            Log::info('WABA Template Send', [
                'phone' => $phone,
                'template' => $template->name,
                'device' => $device->phone,
                'status' => $result['status'],
                'message' => $result['message'] ?? null,
                'error_data' => $result['data'] ?? null,
            ]);

            // 8. Update Device Counter & Record to CRM
            if ($result['status'] == 200) {
                $device->increment('daily_send');

                // 9. Record outbound notification to CRM (history)
                try {
                    $this->recordToCrm($device, $phone, $request, $template, $result['data'] ?? null);
                } catch (\Exception $crmEx) {
                    Log::warning('CRM record failed for WABA notif: ' . $crmEx->getMessage());
                    // Not fatal - message was already sent
                }
            }

            return response()->json([
                'status'    => $result['status'] == 200,
                'message'   => $result['message'],
                'data'      => $result['data'] ?? null
            ], $result['status']);
        } catch (\Exception $e) {

            return response()->json([
                'status'    => false,
                'message'   => 'Terjadi kesalahan sistem',
                'error'     => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'mp4'  => 'video/mp4',
            'avi'  => 'video/x-msvideo',
            'mov'  => 'video/quicktime',
            'mp3'  => 'audio/mpeg',
            'ogg'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'm4a'  => 'audio/mp4',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    private function getWhatsAppMediaType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ])) {
            return 'document';
        }

        return 'document';
    }

    /**
     * Get available device based on settings
     */
    private function getAvailableDevice($settings, $deviceKey = null)
    {
        if ($settings->api_device_use == 'required' && $deviceKey) {
            return WhatsappKeyAccount::where('business_id', $settings->id)
                ->where('id', $deviceKey)
                ->where('status', 'active')
                ->first();
        }

        // Auto-select device based on strategy
        $devicesQuery = WhatsappKeyAccount::where('business_id', $settings->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereRaw('daily_send < limit_per_day')
                    ->orWhere('daily_limit', 'no');
            });

        if ($devicesQuery->count() == 0) {
            return null;
        }

        switch ($settings->whatsapp_sender_notif) {
            case 'sequence':
                return $devicesQuery->orderBy('daily_send', 'asc')->first();

            case 'random':
                return $devicesQuery->inRandomOrder()->first();

            case 'spin':
                // Round-robin: get device with lowest send count
                return $devicesQuery->orderBy('daily_send', 'asc')->first();

            default:
                return $devicesQuery->first();
        }
    }


    /**
     * Build template structure for Facebook Graph API
     */
    private function buildTemplateStructure($templateName, $lang, $header = null, $body = null, $buttons = null)
    {
        $template = [
            'name'      => $templateName,
            'language'  => [
                'code'  => $lang
            ],
            'components' => []
        ];

        // Build Header Component
        if ($header && isset($header['type'])) {
            $headerComponent = [
                'type'          => 'header',
                'parameters'    => []
            ];

            switch ($header['type']) {
                case 'text':
                    $headerComponent['parameters'][] = [
                        'type'  => 'text',
                        'text'  => $header['value']
                    ];
                    break;

                case 'image':
                    $headerComponent['parameters'][] = [
                        'type'  => 'image',
                        'image' => [
                            'link' => $header['value']
                        ]
                    ];
                    break;

                case 'video':
                    $headerComponent['parameters'][] = [
                        'type'  => 'video',
                        'video' => [
                            'link' => $header['value']
                        ]
                    ];
                    break;

                case 'document':
                    $headerComponent['parameters'][] = [
                        'type'      => 'document',
                        'document'  => [
                            'link'      => $header['value'],
                            'filename'  => $header['filename'] ?? 'document.pdf'
                        ]
                    ];
                    break;
            }

            $template['components'][] = $headerComponent;
        }

        // Build Body Component
        if ($body && is_array($body) && count($body) > 0) {
            $bodyComponent = [
                'type'          => 'body',
                'parameters'    => []
            ];

            foreach ($body as $param) {
                $bodyComponent['parameters'][] = [
                    'type'  => 'text',
                    'text'  => (string) $param
                ];
            }

            $template['components'][] = $bodyComponent;
        }

        // Build Button Components
        if ($buttons && is_array($buttons) && count($buttons) > 0) {
            $buttonParameters = [];

            foreach ($buttons as $button) {
                $buttonParam = [
                    'type'      => 'button',
                    'sub_type'  => $button['type'] == 'url' ? 'url' : 'quick_reply',
                    'index'     => $button['index']
                ];

                if ($button['type'] == 'url') {
                    // URL button - dynamic suffix
                    $buttonParam['parameters'] = [
                        [
                            'type'  => 'text',
                            'text'  => $button['value'] // URL suffix
                        ]
                    ];
                } else {
                    // Quick reply button - payload
                    $buttonParam['parameters'] = [
                        [
                            'type'      => 'payload',
                            'payload'   => $button['value']
                        ]
                    ];
                }

                $buttonParameters[] = $buttonParam;
            }

            foreach ($buttonParameters as $btnParam) {
                $template['components'][] = $btnParam;
            }
        }

        return $template;
    }

    /**
     * Normalize phone number to international format
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle Indonesian phone numbers
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 prefix if not exists and not starting with +
        if (substr($phone, 0, 2) !== '62' && substr($phone, 0, 1) !== '+') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send template message via API
     * 
     * @param string $phone
     * @param array $templateContent
     * @param array $variablesData
     * @return array
     */
    public function sendTemplateMessageViaApi($phone, $templateContent, $variablesData = [])
    {
        $url = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$variablesData['phoneid']}/messages";

        $headers = [
            'Authorization' => 'Bearer ' . $variablesData['access_token'],
            'Content-Type'  => 'application/json',
        ];

        $requestData = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phone,
            'type'              => 'template',
            'template'          => $templateContent
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, $requestData);

            if ($response->successful()) {
                return [
                    'status'    => 200,
                    'message'   => 'Pesan berhasil dikirim',
                    'data'      => $response->json()
                ];
            }

            // Handle Facebook API errors
            $errorData = $response->json();
            $errorMessage = 'Gagal mengirim pesan';

            if (isset($errorData['error'])) {
                $errorMessage = $errorData['error']['message'] ??
                    $errorData['error']['error_user_msg'] ??
                    $errorData['error']['error_user_title'] ??
                    $errorMessage;
            }

            return [
                'status'    => $response->status(),
                'message'   => $errorMessage,
                'data'      => $errorData
            ];
        } catch (\Exception $e) {
            return [
                'status'    => 500,
                'message'   => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Record outbound WABA template notification to CRM
     * So it appears in chat history as an outbound message
     */
    private function recordToCrm(
        WhatsappKeyAccount $device,
        string $phone,
        $request,
        $template,
        ?array $metaResponse
    ): void {
        DB::transaction(function () use ($device, $phone, $request, $template, $metaResponse) {
            // Get merchant_id from device
            $merchantId = $device->merchant_id ?? null;
            $businessId = $device->business_id ?? null;

            // Find store for this contact (by phone + merchant)
            $store = Store::where('phone', $phone)
                ->where('merchant_id', $merchantId)
                ->first();

            // If no store exists, create one
            if (!$store) {
                $store = Store::create([
                    'id'          => Str::uuid(),
                    'name'        => $request->name ?? $phone,
                    'phone'       => $phone,
                    'merchant_id' => $merchantId,
                    'business_id' => $businessId,
                    'status'      => 'no',
                    'prospek'     => 'pending',
                    'position'    => 0,
                ]);
            }

            // Find or create history_chats record for this contact
            $historyChat = HistoryChat::firstOrCreate(
                [
                    'device_id'        => null,
                    'from_number'      => $phone,
                    'type'             => 'personal',
                    'from'             => 'waba',
                    'livechat_id'      => null,
                    'whatsapp_waba_id' => $device->id,
                    'telegram_id'      => null,
                    'instagram_id'     => null,
                    'messanger_id'     => null,
                ],
                [
                    'id'          => Str::uuid(),
                    'merchant_id' => $merchantId,
                    'business_id' => $businessId,
                    'name'        => $request->name ?? $phone,
                    'status'      => 'open',
                    'from'        => 'waba',
                    'store_id'    => $store->id,
                ]
            );

            // Update store_id if not set (existing history_chat without store)
            if (!$historyChat->store_id && $store) {
                $historyChat->update(['store_id' => $store->id]);
            }

            // Update store with history_chat_id if not set
            if (!$store->history_chat_id) {
                $store->update(['history_chat_id' => $historyChat->id]);
            }

            // Build message text from template body
            $templateBody = '';
            if ($template && $template->message) {
                $templateMsg = json_decode($template->message, true);
                foreach ($templateMsg['components'] ?? [] as $comp) {
                    if ($comp['type'] === 'BODY') {
                        $templateBody = $comp['text'] ?? '';
                        // Replace variables with actual values from request
                        if ($request->body && is_array($request->body)) {
                            foreach ($request->body as $idx => $val) {
                                $num = $idx + 1;
                                $templateBody = str_replace("{{" . $num . "}}", $val, $templateBody);
                            }
                        }
                        break;
                    }
                }
            }

            // Get Meta message ID from response if available
            $metaMsgId = $metaResponse['messages'][0]['id'] ?? null;

            // Create the outbound message record
            $outboundDetail = HistoryChatDetail::create([
                'id'              => Str::uuid(),
                'history_chat_id' => $historyChat->id,
                'from'            => 'device',
                'message'         => $templateBody ?: '[Template: ' . ($template->name ?? 'unknown') . ']',
                'type'            => 'template',
                'messageid'       => $metaMsgId,
                'is_read'         => 'no',
            ]);

            // Update last_message_at and unread_count on history_chats
            $historyChat->update([
                'last_message_at' => now(),
                'unread_count'    => 0, // outbound, tidak nambah unread agent
            ]);

            // === Real-time notify CRM agents (triggerEmit equivalent) ===
            try {
                $expressUrl = config('services.express.url') . '/trigger-whatsapp';
                $outboundDetail->load(['repliedMessage']);
                Http::timeout(5)->post($expressUrl, HistoryChatResources::make($outboundDetail)->toArray(request()));
            } catch (\Exception $emitEx) {
                Log::warning('WABA outbound emit failed: ' . $emitEx->getMessage());
            }
        });
    }
}
