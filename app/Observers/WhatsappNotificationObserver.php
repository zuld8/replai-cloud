<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class WhatsappNotificationObserver
{
    public function whatsAppNotif(array $data)
    {
        if (strval($data['phone']) != null) {
            $message = array(
                "authkey"       => $data['whatsapp_key'],
                "appkey"        => $data['whatsapp_session'],
                'to'            => strval($data['phone']),
                'message'       => $data['message'],
                'file'          => $data['file']
            );

            return Http::accept('application/json')->post(config('app.url').'/api/create-message', $message);
        }
    }
}
