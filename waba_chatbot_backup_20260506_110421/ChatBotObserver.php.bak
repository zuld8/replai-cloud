<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\ChatBotImage;
use App\Models\MetaAccount;
use App\Models\WhatsappKeyAccount;
use Illuminate\Http\Request;

class ChatBotObserver
{

    public function getData(Request $request)
    {
        return ChatBot::where(function ($q) use ($request) {
            return $request->keyword ? $q->where('keyword', 'like', '%' . $request->keyword . '%') : '';
        })->orderBy('created_at', 'desc');
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $chatbotLimitation  = chatbot_limitation(my_business());
            if (!$chatbotLimitation) {
                return false;
            }
        }

        return true;
    }

    public function createData(Request $request)
    {
        return ChatBot::create([
            'keyword'           => $request->keyword,
            'select_device'     => !empty($request->device) ? implode(",", $request->device) : null,
            'select_livechat'   => !empty($request->livechats) ? implode(",", $request->livechats) : null,
            'select_instagram'   => !empty($request->instagrams) ? implode(",", $request->instagrams) : null,
            'select_messanger'   => !empty($request->messengers) ? implode(",", $request->messengers) : null,
            'select_telegram'   => !empty($request->telegrams) ? implode(",", $request->telegrams) : null,
            'reply_method'      => $request->method,
            'template_id'       => $request->method == 'template' ? $request->template : null,
            'message'           => $request->method == 'text' ? $request->message : null
        ]);
    }

    public function createDataForOfficial(Request $request, MetaAccount $meta, String $image = '')
    {
        $text   = '';
        if ($request->method == 'text') {
            $data   = json_decode($request->metadata, true);
            $text   = $data['body']['text'];
        }

        return ChatBot::create([
            'keyword'           => $request->keyword,
            'reply_method'      => $request->method,
            'select_device'     => $request->devices,
            'template_id'       => $request->method == 'template' ? $request->template : null,
            'message'           => $request->method == 'text' ? $text : null,
            'meta_account_id'   => $meta->id,
            'metadata'          => $request->metadata,
            'file'              => $image
        ]);
    }

    public function updateDataForOfficial(Request $request, MetaAccount $meta, ChatBot $bot, String $image = '')
    {

        $text   = '';
        if ($request->method == 'text') {
            $data   = json_decode($request->metadata, true);
            $text   = $data['body']['text'];
        }

        $bot->update([
            'keyword'           => $request->keyword,
            'reply_method'      => $request->method,
            'select_device'     => $request->devices,
            'template_id'       => $request->method == 'template' ? $request->template : null,
            'message'           => $request->method == 'text' ? $text : null,
            'meta_account_id'   => $meta->id,
            'metadata'          => $request->metadata,
            'file'              => $image == '' ? $bot->file : $image
        ]);
    }


    public function updateData(Request $request, ChatBot $bot)
    {

        $bot->update([
            'keyword'           => $request->keyword,
            'select_device'     => !empty($request->device) ? implode(",", $request->device) : null,
            'select_livechat'   => !empty($request->livechats) ? implode(",", $request->livechats) : null,
            'select_instagram'   => !empty($request->instagrams) ? implode(",", $request->instagrams) : null,
            'select_messanger'   => !empty($request->messengers) ? implode(",", $request->messengers) : null,
            'select_telegram'   => !empty($request->telegrams) ? implode(",", $request->telegrams) : null,
            'reply_method'      => $request->method,
            'template_id'       => $request->method == 'template' ? $request->template : null,
            'message'           => $request->method == 'text' ? $request->message : null
        ]);
    }

    public function deleteData(ChatBot $bot)
    {
        return $bot->delete();
    }

    public function createImages(Request $request, ChatBot $chatBot)
    {
        $chatBot->details()->delete();

        if (isset($request->url)) {
            $i = 0;
            while ($i < count($request->url)) {
                ChatBotImage::create([
                    'chatbot_id'     => $chatBot->id,
                    'url'            => $request->url[$i],
                    'name'           => $request->name[$i] ?? null
                ]);
                $i++;
            }
        }
    }

    public function deleting(ChatBot $chatBot)
    {
        $chatBot->details()->delete();
    }
}
