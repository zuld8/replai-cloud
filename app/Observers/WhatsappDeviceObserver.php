<?php

namespace App\Observers;

use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappDeviceObserver
{
    public function getData(Request $request)
    {
        return WhatsappDevice::where(function ($q) use ($request) {
            return $request->phone ? $q->where('phone', 'like', '%' . $request->phone . '%') : '';
        })->whereHas('agents', function ($q) {
            $q->where('user_id', my_user()->id);
        })->where(function ($q) use ($request) {
            return $request->limit ? ($request->limit == 'limit' ? $q->whereRaw('daily_send >= limit_per_day') : $q->whereRaw('daily_send < limit_per_day')->orWhere("daily_limit", "no")) : '';
        })->orderBy('phone', 'asc');
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $deviceLimitation  = device_limitation(my_business());
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

        $device = WhatsappDevice::create([
            'name'                  => $request->name,
            'phone'                 => $request->phone,
            'agent'                 => implode(",", $agents),
            'phone_notification'    => $request->notification ?? 'no',
            'chat_history'          => $request->chat_history ?? 'no',
            'limit_per_day'         => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method'     => $request->method,
            'fine_tunnel_id'        => $request->method == 'ai' || $request->method == 'all' ? $request->tunnel : null,
            'daily_limit'               => $request->daily_limit ?? 'no',
            'auto_reply_certain_day'    => $request->certain_day ?? 'no',
            'days'                      => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time'   => $request->certain_time ?? 'no',
            'start_time'                => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time'                  => $request->certain_time == 'yes' ? $request->end_time : null,
            'webhook'                   => $request->webhook,
            'auto_read_before_autorespon'       => $request->auto_read_chatbot ?? 'no',
            'auto_reply_option'         => $request->auto_reply_option ?? 'no',
        ]);

        $device->agents()->sync($agents);

        return $device;
    }

    public function updateData(Request $request, WhatsappDevice $device)
    {

        $agents = $request->agent ?? [];
        $currentUserId = my_user()->id;

        if (!in_array($currentUserId, $agents)) {
            $agents[] = $currentUserId;
        }

        $device->update([
            'name'                  => $request->name,
            'agent'                 => implode(",", $agents),
            'limit_per_day'         => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method'     => $request->method,
            'fine_tunnel_id'        => $request->method == 'ai' || $request->method == 'all' ? $request->tunnel : null,
            'daily_limit'               => $request->daily_limit ?? 'no',
            'auto_reply_certain_day'    => $request->certain_day ?? 'no',
            'days'                      => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time'   => $request->certain_time ?? 'no',
            'start_time'                => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time'                  => $request->certain_time == 'yes' ? $request->end_time : null,
            'webhook'                   => $request->webhook,
            'auto_read_before_autorespon'       => $request->auto_read_chatbot ?? 'no',
            'auto_reply_option'         => $request->auto_reply_option ?? 'no',
        ]);

        $device->agents()->sync($agents);
    }

    public function deleteData(WhatsappDevice $device)
    {
        $device->delete();
    }

    public function setAutoReply(WhatsappDevice $whatsapp, Request $request)
    {
        $whatsapp->update([
            'reply_any_chat'            => $request->reply_chat,
            'reply_method'              => $request->reply_method,
            'template_id'               => $request->reply_method == 'template' ? $request->reply_template : null,
            'reply_text'                => $request->reply_method == 'text' ? $request->reply_text : null
        ]);
    }

    public function deleting(WhatsappDevice $device)
    {

        if ($device->status == 'active') {
            Http::delete(config('custom.whatsapp_server_url') . '/sessions/delete/device_' . $device->id);
        }
    }
}
