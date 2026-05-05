<?php

namespace App\Observers;

use App\Models\LiveChat;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class LiveChatObserver
{
    public function getData(Request $request)
    {
        return LiveChat::where(function ($q) use ($request) {
            return $request->phone ? $q->where('name', 'like', '%' . $request->phone . '%') : '';
        })->whereHas('agents', function ($q) {
            $q->where('user_id', my_user()->id);
        })->orderBy('name', 'asc');
    }

    public function getById($idToken)
    {
        return LiveChat::findOrFail($idToken);
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $livechatLimitation  = live_chat_limitation(my_business());
            if (!$livechatLimitation) {
                return false;
            }
        }

        return true;
    }

    public function createData(Request $request, String $image = '')
    {
        $agents = $request->agent ?? [];

        $livechat = LiveChat::create([
            'name'                  => $request->name,
            'description'           => $request->description,
            'agent'                 => implode(",", $agents),
            'type'                  => $request->type,
            'photo'                 => $image,
            'finetunnel_id'         => $request->type == 'ai' || $request->type == 'all' ? $request->tunnel : null,
            'faqs'                  => !empty($request->question) ? json_encode($request->question) : json_encode([]),
            'sosmed'                => !empty($request->url) && !empty($request->label) && !empty($request->icon)
                ? json_encode(array_map(function ($url, $label, $icon) {
                    return [
                        'link' => $url,
                        'label' => $label,
                        'icon_link' => $icon
                    ];
                }, $request->url, $request->label, $request->icon))
                : json_encode([])
        ]);

        $livechat->agents()->sync($agents);

        return $livechat;
    }

    public function updateData(Request $request, LiveChat $chat, String $image = '')
    {
        $agents = $request->agent ?? [];

        // Update livechat
        $chat->update([
            'name'                  => $request->name,
            'description'           => $request->description,
            'agent'                 => implode(",", $agents), // ✅ Tetap update untuk backward compatibility
            'type'                  => $request->type,
            'finetunnel_id'         => $request->type == 'ai' || $request->type == 'all' ? $request->tunnel : null,
            'photo'                 => $image == '' ? $chat->photo : $image,
            'faqs'                  => !empty($request->question) ? json_encode($request->question) : json_encode([]),
            'sosmed'                => !empty($request->url) && !empty($request->label) && !empty($request->icon)
                ? json_encode(array_map(function ($url, $label, $icon) {
                    return [
                        'link' => $url,
                        'label' => $label,
                        'icon_link' => $icon
                    ];
                }, $request->url, $request->label, $request->icon))
                : json_encode([])
        ]);

        $chat->agents()->sync($agents);
    }

    public function deleteData(LiveChat $chat)
    {
        $chat->delete();
    }
}
