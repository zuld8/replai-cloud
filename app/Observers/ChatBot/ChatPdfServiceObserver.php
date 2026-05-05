<?php

namespace App\Observers\ChatBot;

use Illuminate\Support\Facades\Http;

class ChatPdfServiceObserver
{
    public function getQuestion(String $key, $message, $description, $conversations = null)
    {

        $messages   = [];

        if ($conversations != null) {
            foreach ($conversations as $conversation) {
                $messages[] = [
                    'role'      => $conversation->from == 'user' ? 'user' : 'assistant',
                    'content'   => $conversation->message,
                ];
            }
        }

        $messages[] = [
            'role'      => 'user',
            'content'   => $message,
        ];

        return Http::withHeaders([
            'x-api-key'     => $key,
            'Content-Type'  => 'application/json',
        ])->post('https://api.chatpdf.com/v1/chats/message', [
            'referenceSources'  => true,
            'sourceId'          => $description,
            'messages'          => $messages
        ]);
    }
}
