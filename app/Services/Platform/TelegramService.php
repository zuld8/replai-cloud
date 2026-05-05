<?php

namespace App\Services\Platform;

use App\Models\ChatBot\HistoryChatDetail;
use App\Models\TelegramKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function getData(Request $request)
    {
        return TelegramKey::where(function ($q) use ($request) {
            return $request->token ? $q->where('token', 'like', '%' . $request->token . '%') : '';
        })->whereHas('agents', function ($q) {
            $q->where('user_id', my_user()->id);
        })->where(function ($q) use ($request) {
            return $request->limit ? ($request->limit == 'limit' ? $q->whereRaw('daily_send >= limit_per_day') : $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no")) : '';
        })->orderBy('token', 'asc');
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $deviceLimitation  = telegram_limitation(my_business());
            if (!$deviceLimitation) {
                return false;
            }
        }


        return true;
    }

    public function createData(Request $request)
    {

        $agents = $request->agent ?? [];
        $currentUserId = my_user()->id;

        if (!in_array($currentUserId, $agents)) {
            $agents[] = $currentUserId;
        }


        $telegram = TelegramKey::create([
            'name'                          => $request->name,
            'token'                         => $request->token,
            'agent'                         => implode(",", $agents),
            'limit_per_day'                 => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method'             => $request->method,
            'fine_tunnel_id'                => $request->method == 'ai' || $request->method == 'all' ? $request->tunnel : null,
            'daily_limit'                   => $request->daily_limit,
            'auto_reply_certain_day'        => $request->certain_day,
            'days'                          => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time'       => $request->certain_time,
            'status'                        => 'active',
            'start_time'                    => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time'                      => $request->certain_time == 'yes' ? $request->end_time : null,
        ]);

        $telegram->agents()->sync($agents);
    }

    public function updateData(Request $request, TelegramKey $telegram)
    {

        $agents = $request->agent ?? [];
        $currentUserId = my_user()->id;

        if (!in_array($currentUserId, $agents)) {
            $agents[] = $currentUserId;
        }


        $telegram->update([
            'name'                          => $request->name,
            'agent'                         => implode(",", $agents),
            'limit_per_day'                 => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method'             => $request->method,
            'fine_tunnel_id'                => $request->method == 'ai' || $request->method == 'all' ? $request->tunnel : null,
            'daily_limit'                   => $request->daily_limit,
            'auto_reply_certain_day'        => $request->certain_day,
            'days'                          => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time'       => $request->certain_time,
            'status'                        => $request->status,
            'start_time'                    => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time'                      => $request->certain_time == 'yes' ? $request->end_time : null,
        ]);

        $telegram->agents()->sync($agents);
    }

    public function deleteData(TelegramKey $telegram)
    {
        $telegram->delete();
    }

    public function setWebhook(TelegramKey $telegram)
    {
        return Http::post('https://api.telegram.org/bot' . $telegram->token . '/setWebhook', [
            'url'   => config('app.url') . '/api-app/telegram/callback/' . $telegram->id
        ]);
    }

    public function removeWebhook(TelegramKey $telegram)
    {
        $url = "https://api.telegram.org/bot{$telegram->token}/deleteWebhook";
        return Http::post($url);
    }

    public function sendMessage(TelegramKey $telegram, $chatId, $message = '', $file = null, $forReply = null)
    {
        $token          = $telegram->token;
        $chatWillReply  = !$forReply ? null : HistoryChatDetail::where('messageid', $forReply->messageid)->first(['messageid']);

        $endpoint   = 'sendMessage';
        $payload    = [
            'chat_id' => $chatId,
            'text'    => $message,
        ];

        if ($file) {
            $ext = strtolower(pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $endpoint = 'sendPhoto';
                $payload = [
                    'chat_id' => $chatId,
                    'caption' => $message,
                    'photo'   => $file,
                ];
            } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                $endpoint = 'sendVideo';
                $payload = [
                    'chat_id' => $chatId,
                    'caption' => $message,
                    'video'   => $file,
                ];
            } else {
                $endpoint = 'sendDocument';
                $payload = [
                    'chat_id'  => $chatId,
                    'caption'  => $message,
                    'document' => $file,
                ];
            }
        }


        if ($chatWillReply) {
            $payload['reply_to_message_id'] = $chatWillReply->messageid;
        }


        $url    = "https://api.telegram.org/bot{$token}/{$endpoint}";

        return Http::post($url, $payload);
    }
}
