<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatApp\SendWhatsappMessageRequest;
use App\Models\WhatsappDevice;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappServiceObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SendMessageController extends Controller
{

    protected $whatsappServiceObserver;
    protected $templateObserver;
    protected $uploadImageProcess;

    public function __construct(WhatsappServiceObserver $whatsappServiceObserver, TemplateObserver $templateObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->whatsappServiceObserver  = $whatsappServiceObserver;
        $this->templateObserver         = $templateObserver;
        $this->uploadImageProcess       = $uploadImageProcess;
    }

    public function create(Request $request, WhatsappDevice $device)
    {
        $templates      = $this->templateObserver->getData($request)->where('type', 'whatsapp')->where('for_waba', 'no')->get(['id', 'name']);
        return view('device.message', ['page'    => 'Whatsapp Message Send Test'], compact('device', 'templates'));
    }

    public function getChatList(Request $request, WhatsappDevice $device)
    {
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
                    $data['sender']     = isset($message->message) ? $message->message->key->fromMe : false;
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
                'message'       => 'Successfully Retrieved message contact data',
                'list'          => $chats,
                'total_chat'    => count($chats),
                'last_chat'     => $chats->first() ? $chats->first()['timestamp'] : $request->last_chat ?? '',
                'status'        => true
            ], 200);
        } else {
            return response()->json([
                'message'       => 'Failed to retrieve message contact data, please check internet connection and refresh page',
                'status'        => false
            ], 419);
        }
    }

    public function getContactList(Request $request, WhatsappDevice $device)
    {

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
                'message'       => 'Successfully Retrieved contact data',
                'list'          => $contacts,
                'total_chat'    => count($contacts),
                'status'        => true
            ], 200);
        } else {
            return response()->json([
                'message'       => 'Failed to retrieve message data, please check internet connection and refresh page',
                'status'        => false
            ], 419);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 13. Get Chats Details
    |--------------------------------------------------------------------------
    */

    public function getChatDetails(Request $request, WhatsappDevice $device, $chatId)
    {
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
            'message'       => 'Successfully retrieved message data',
            'autoread'      => $device->auto_read_in_chattapp == 'yes' ? true : false,
            'chatid'        => $chatId,
            'list'          => $chats,
            'last_chat'     => $chats->first() ? $chats->first()['timestamp'] : $request->last_id ?? '',
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 14. Send Message
    |--------------------------------------------------------------------------
    */

    public function sendMessage(SendWhatsappMessageRequest $request, WhatsappDevice $device)
    {


        $file   = '';
        if ($request->type == 'photo') {
            $photo = explode(',', $request->photo);
            if (count($photo) == 2 && substr($photo[0], 0, 5) === 'data:') {
                $imageData = base64_decode($photo[1]);

                if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                    $photoName  = 'photo-' . $device->id . date('Ymdhis');
                    $file = $this->uploadImageProcess->uploadFile($request->photo, $photoName, 'uploads/camera/', false);

                    if ($file != null) {
                        $file   = asset($file);
                    }
                }
            }
        } else {
            $file   = $request->file ? asset($this->uploadImage($request, 'file', 'wamedia')) : '';
        }

        $phone  = explode("-", $request->phone);
        $this->whatsappServiceObserver->sendMessage($phone[0], $device->id, $request->text, $file, $request->type, null, ($request->isgroup == 'true' ? true : false));


        return response()->json([
            'message'       => __('master.device.success_send'),
            'status'        => true
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
}
