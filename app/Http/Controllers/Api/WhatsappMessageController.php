<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\WhatsappMessageRequest;
use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsappMessageController extends Controller
{

    protected $whatsappServiceObserver;

    public function __construct(WhatsappServiceObserver $whatsappServiceObserver)
    {
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
    }

    public function message(WhatsappMessageRequest $request)
    {


        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id', 'api_device_use', 'whatsapp_sender_notif']);
        $type       = 'description';
        $datas      = null;
        $file       = $request->file ?? '';

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Cannot be recognized'
            ], 401);
        }

        $device = null;

        if ($settings->api_device_use == 'required') {
            $device     = WhatsappDevice::where("business_id", $settings->id)->where("id", $request->device_key)->first();
        } else {
            $devices     = WhatsappDevice::where(function ($q) {
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

        if ($device->daily_limit == 'yes') {
            if ($device->daily_send >= $device->limit_per_day) {
                return response()->json([
                    'message'       => 'This whatsapp device`s sending limit has expired',
                    'status'        => false
                ], 422);
            }
        }

        $device->update([
            'daily_send'    => $device->daily_send + 1
        ]);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device tidak dapat dikenali'
            ], 401);
        }

        if ($request->method == 'text') {
            $message    = $request->text; 
        }

        if ($request->method == 'template') {
            $template   = MessageTemplate::find($request->template);

            if (!$template) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Template tidak dikenali'
                ], 401);
            }

            $message    = $this->variableToTemplate($template->message, $request->variables ?? []);
            $type       = $template->type_content;
            $datas      = json_decode($template->button_or_list, true);
            $file       = $file == '' ? ($template->image != null ? $template->media_data : '') : $file;
        }

        $sendMessage    = $this->whatsappServiceObserver->sendMessage($request->phone, $device->id, $message, $file, $type, $datas, $request->is_group ?? false);

        return response()->json([
            'status'    => $sendMessage['status'] == 200 ? true : false,
            'message'   => $sendMessage['message']
        ], $sendMessage['status']);
    }

    private function variableToTemplate($message = '', $variables = [])
    {
        $messageJoinVariable = $message;

        foreach ($variables ?? [] as $key => $value) {
            $messageJoinVariable = str_replace($key, $value, $messageJoinVariable);
        }

        return $messageJoinVariable;
    }

    public function setStatusDevice($device, $status = '')
    {

        $device_id      = str_replace('device_', '', $device);
        $device         = WhatsappDevice::find($device_id);

        if ($status == 0) {
            if ($device) {
                $device->update([
                    'status'        => 'no_active'
                ]);
            }
        }
        return response()->json([
            'message'       => 'Status berhasil di perbaharui'
        ], 200);
    }

    public function getChatList(Request $request, $device)
    {

        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Cannot be recognized'
            ], 401);
        }

        $device         = WhatsappDevice::where("business_id", $settings->id)->where("id", $device)->first();
        $isGroup        = $request->is_group ?? 'false';
        $session_id     = 'device_' . $device->id;
        $chats          = [];

        $response       = $this->whatsappServiceObserver->getChats($session_id, $isGroup, $request->last_chat);
        $status         = $response->status();

        if ($status == 200) {
            $responseBody  = json_decode($response->body());
            $colllections  = collect($responseBody->data);

            $contacts = $colllections->filter(function ($item) use ($device) {
                return (explode('@', $item->id)[0] != $device->phone);
            })->map(function ($item) use ($device) {

                $phone              = explode('@', $item->id);

                if ($phone[0] != $device->phone) {
                    $timestamp  = $this->getConversationTimestamp($item->conversationTimestamp);
                    $dateTime   = Carbon::createFromTimestamp($timestamp);
                    $time       = $dateTime->timezone(user_setting()->timezone)->format('Y-m-d H:i:s');

                    $phone              = explode('@', $item->id);
                    $message            = isset($item->messages[0]) ? $item->messages[0] : null;
                    $text               = '';
                    $type               = null;


                    if (isset($message->message->message->conversation)) {
                        $type           = 'text';
                        $text        = $message->message->message->conversation ?? '';
                    }

                    if (isset($message->message->message->extendedTextMessage)) {
                        $type           = 'text';
                        $text        = $message->message->message->extendedTextMessage->text ?? '';
                    }

                    if (isset($item->message->imageMessage)) {
                        $type           = 'image';
                        $text           = isset($item->message->imageMessage->caption) ? $item->message->imageMessage->caption : 'Photo';
                    }

                    if (isset($item->message->videoMessage)) {
                        $type           = 'video';
                        $text           = isset($item->message->videoMessage->caption) ? $item->message->videoMessage->caption : 'Video';
                    }

                    if (isset($item->message->audioMessage)) {
                        $type           = 'audio';
                        $text           = 'Audio';
                    }

                    if (isset($item->message->locationMessage)) {
                        $type           = 'location';
                        $text           = 'Share Location';
                    }

                    if (isset($message->message->message->type)) {

                        if ($message->message->message->type != 'location') {
                            $type           = $message->message->message->type ?? '';
                            $text           = ($message->message->message->caption ?? '') == "" || ($message->message->message->caption ?? '') == null ? $this->sendType($message->message->message->type ?? '') : ($message->message->message->caption ?? '');
                        } else {
                            $type           = 'location';
                            $text           = 'Share Location';
                        }
                    }

                    $data['id']         = strtolower(preg_replace("/[^0-9a-zA-Z]/", "-", $item->id));
                    $data['type']       = $type;
                    $data['sender']     = $message->message->key->fromMe;
                    $data['uid']        = $item->id;
                    $data['message']    = $text;
                    $data['phone']      = $phone[0] ?? null;
                    $data['photo']      = asset('assets/img/icons/user.png');
                    $data['name']       = isset($item->name) ? $item->name : $phone[0] ?? '';
                    $data['unread']     = abs($item->unreadCount ?? 0);
                    $data['time']       = time_format($time);
                    $data['timedate']   = $time;
                    $data['timestamp']  = $timestamp;
                    return $data;
                }
            })->sortByDesc('timedate')->values();

            $chats                  = $contacts;

            return response()->json([
                'message'       => 'Berhasil Mengambil data kontak pesan',
                'list'          => $chats,
                'total_chat'    => count($chats),
                'last_chat'     => $chats->first() ? $chats->first()['timestamp'] : $request->last_chat ?? '',
                'status'        => true
            ], 200);
        } else {
            return response()->json([
                'message'       => 'gagal mengambil data kontak pesan, silahkan ulangi dengan me-refresh halaman',
                'status'        => false
            ], 419);
        }
    }

    public function getContactList(Request $request, $device)
    {

        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Cannot be recognized'
            ], 401);
        }

        $device         = WhatsappDevice::where("business_id", $settings->id)->where("id", $device)->first();

        $session_id     = 'device_' . $device->id;
        $contacts       = [];
        $response       = $this->whatsappServiceObserver->getContacts($session_id);
        $status         = $response->status();


        if ($status == 200) {
            $responseBody  = json_decode($response->body());
            $colllections  = collect($responseBody->data);

            $contacts = $colllections->filter(function ($item) {
                return (explode('@', $item->id)[1] == 's.whatsapp.net');
            })->map(function ($item) {

                $phone              = explode('@', $item->id);
                $data['id']         = $item->id;
                $data['photo']      = asset('assets/img/icons/user.png');
                $data['phone']      = $phone[0];
                $data['name']       = $item->name ?? $phone[0];
                $data['getpict']    = false;
                return $data;
            })->sortByDesc('name')->values();

            return response()->json([
                'message'       => 'Berhasil Mengambil data kontak',
                'list'          => $contacts,
                'total_chat'    => count($contacts),
                'status'        => true
            ], 200);
        } else {
            return response()->json([
                'message'       => 'gagal mengambil data kontak pesan, silahkan ulangi dengan me-refresh halaman',
                'status'        => false
            ], 419);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 13. Get Chats Details
    |--------------------------------------------------------------------------
    */

    public function getChatDetails(Request $request, $device, $chatId)
    {

        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Cannot be recognized'
            ], 401);
        }

        $device         = WhatsappDevice::where("business_id", $settings->id)->where("id", $device)->first();

        $session_id     = 'device_' . $device->id;
        $chatId         = explode("-", $chatId);
        $isGroup        = $request->is_group ?? 'false';
        $chatId         = $chatId[0] . ($isGroup == 'false' ? '@s.whatsapp.net' : '@g.us');
        $chats          = [];
        $response       = $this->whatsappServiceObserver->getChatDetail($session_id, $chatId);
        $status         = $response->status();


        if ($status == 200) {
            $responseBody  = json_decode($response->body());
            $colllections  = collect($responseBody->data);

            $messages = $colllections->filter(function ($item) use ($request) {
                return $request->last_id ? (int)$item->messageTimestamp > $request->last_id : true;
            })->sortByDesc('messageTimestamp')->map(function ($item) {

                $time               = '';
                if (isset($item->messageTimestamp)) {
                    $timestamp      = $item->messageTimestamp;
                    $dateTime       = Carbon::createFromTimestamp($timestamp);
                    $time           = $dateTime->format('Y-m-d H:i:s');
                }

                $type       = null;
                $mime       = '';
                $textUrl    = null;

                if (isset($item->message->conversation)) {
                    $type           = 'text';
                    $desc           = [
                        'asset'         => false,
                        'text'          => $item->message->conversation ?? ''
                    ];
                }

                if (isset($item->message->extendedTextMessage)) {
                    $type           = 'text';
                    $textUrl        = $item->message->extendedTextMessage->matchedText ?? null;
                    $desc           = [
                        'asset'         => false,
                        'text'          => $item->message->extendedTextMessage->text ?? ''
                    ];
                }

                if (isset($item->message->imageMessage)) {
                    $type           = 'image';
                    $mime           = $item->message->imageMessage->mimetype ?? '';
                    $mime           = $mime != '' ? get_mime_format($mime) : '';
                    $url            = null;
                    $asset          = false;

                    if (file_exists("uploads/wamedia/{$item->key->id}.{$mime}")) {
                        $url        = asset("uploads/wamedia/{$item->key->id}.{$mime}");
                        $asset      = true;
                    }

                    $desc           = [
                        'asset'         => $asset,
                        'url'           => $url,
                        'text'          => isset($item->message->imageMessage->caption) ? $item->message->imageMessage->caption : '',
                    ];
                }

                if (isset($item->message->videoMessage)) {
                    $type           = 'video';
                    $mime           = $item->message->videoMessage->mimetype ?? '';
                    $mime           = $mime != '' ? get_mime_format($mime) : '';
                    $url            = null;
                    $asset          = false;

                    if (file_exists("uploads/wamedia/{$item->key->id}.{$mime}")) {
                        $url        = asset("uploads/wamedia/{$item->key->id}.{$mime}");
                        $asset      = true;
                    }

                    $desc           = [
                        'asset'         => $asset,
                        'url'           => $url,
                        'text'          => isset($item->message->videoMessage->caption) ? $item->message->videoMessage->caption : '',
                    ];
                }

                if (isset($item->message->audioMessage)) {
                    $type           = 'audio';
                    $mime           = $item->message->audioMessage->mimetype ?? '';
                    $mime           = $mime != '' ? get_mime_format($mime) : '';
                    $url            = null;
                    $asset          = false;

                    if (file_exists("uploads/wamedia/{$item->key->id}.{$mime}")) {
                        $url        = asset("uploads/wamedia/{$item->key->id}.{$mime}");
                        $asset      = true;
                    }

                    $desc           = [
                        'asset'         => $asset,
                        'url'           => $url,
                        'text'          => '',
                    ];
                }

                if (isset($item->message->documentWithCaptionMessage)) {
                    $documentCapt   = $item->message->documentWithCaptionMessage;
                    $message        = $documentCapt->message->documentMessage;
                    $mime           = $message->mimetype ?? '';
                    $mime           = $mime != '' ? get_mime_format($mime) : '';
                    $url            = null;
                    $asset          = false;

                    if (file_exists("uploads/wamedia/{$item->key->id}.{$mime}")) {
                        $url        = asset("uploads/wamedia/{$item->key->id}.{$mime}");
                        $asset      = true;
                    }

                    $type           = 'document';
                    $desc           = [
                        'asset'         => $asset,
                        'title'         => $documentCapt->fileName ?? '',
                        'url'           => $url,
                        'text'          => isset($message->caption) ? $message->caption : '',
                    ];
                }

                if (isset($item->message->documentMessage)) {
                    $documentCapt   = $item->message->documentMessage;
                    $mime           = $documentCapt->mimetype ?? '';
                    $mime           = $mime != '' ? get_mime_format($mime) : '';
                    $url            = null;
                    $asset          = false;

                    if (file_exists("uploads/wamedia/{$item->key->id}.{$mime}")) {
                        $url        = asset("uploads/wamedia/{$item->key->id}.{$mime}");
                        $asset      = true;
                    }

                    $type           = 'document';
                    $desc           = [
                        'asset'         => $asset,
                        'title'         => $documentCapt->fileName ?? '',
                        'url'           => $url,
                        'text'          => isset($documentCapt->caption) ? $documentCapt->caption : '',
                    ];
                }

                if (isset($item->message->locationMessage)) {
                    $locationData   = $item->message->locationMessage;
                    $type           = 'location';
                    $desc           = [
                        'latitude'      => $locationData->degreesLatitude,
                        'longitude'     => $locationData->degreesLongitude,
                        'text'          => '',
                        'url'           => "https://www.google.com/maps?q={$locationData->degreesLatitude},{$locationData->degreesLongitude}",
                    ];
                }

                if ($type != null) {
                    $data['id']         = $item->key->id;
                    $data['message']    = [
                        'type'              => $type,
                        'url'               => $textUrl,
                        'mime'              => $mime,
                        'detail'            => $desc,
                    ];
                    $data['time']       = substr($time, 11, 5);
                    $data['timestamp']  = $item->messageTimestamp;
                    $data['sender']     = $item->key->fromMe;
                    $data['details']    = $item;
                    $data['for_read']   = [
                        "remoteJid"         => $item->key->remoteJid ?? '',
                    ];
                    $data['status']     = isset($item->status) ? $item->status : 'UNREAD';
                    return $data;
                }
            });

            $chats                  = $messages;
            $chats                  = $chats->filter(function ($item) {
                return isset($item['id']);
            });
        }


        return response()->json([
            'message'       => 'Berhasil mengambil data pesan',
            'autoread'      => $device->auto_read_in_chattapp == 'yes' ? true : false,
            'chatid'        => $chatId,
            'list'          => $chats,
            'last_chat'     => $chats->first() ? $chats->first()['timestamp'] : $request->last_id ?? '',
        ], 200);
    }

    function getConversationTimestamp($timestamp)
    {
        if (is_integer($timestamp)) {
            return $timestamp;
        } else {

            if (isset($timestamp->low)) {
                if (isset($timestamp->low->low)) {
                    return $timestamp->low->low ?? 0;
                }

                return $timestamp->low ?? 0;
            } else {
                $timestamp->low ?? 0;
            }

            return 0;
        }
    }

    function sendType($type)
    {
        if ($type == 'image') {
            return 'Photo';
        }

        if ($type == 'video') {
            return 'Video';
        }

        if ($type == 'audio') {
            return 'Suara';
        }

        if ($type == 'document') {
            return 'Dokument';
        }

        return '';
    }

    public function checkingDevice(Request $request)
    {
        $setting    = Setting::where("local_api_key", $request->api_key)->first();

        if (!$setting) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key tidak di temukan'
            ], 401);
        }

        $device     = WhatsappDevice::where("id", $request->device)->where("business_id", $setting->id)->first();

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Perangkat Device tidak di temukan'
            ], 401);
        }

        if ($device->status == 'no_active') {
            return response()->json([
                'status'    => false,
                'message'   => 'Perangkat Device tidak aktif, silahkan scan qr perangkat terlebih dahulu'
            ], 419);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Perangkat ditemukan'
        ], 200);
    }

    public function getTemplates(Request $request)
    {
        $setting    = Setting::where("local_api_key", $request->api_key)->first();

        if (!$setting) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key tidak di temukan'
            ], 401);
        }

        $templates  = MessageTemplate::where("business_id", $setting->id)->where("type", "whatsapp")->where("for_waba", "no")->get(['id', 'name']);

        return response()->json([
            'status'    => true,
            'templates' => $templates
        ], 200);
    }
}
