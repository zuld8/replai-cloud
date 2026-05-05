<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessagestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $replyTo = null;
        $replyType = 'text';
        $replyMediaUrl = null;
        $replyId = null;
        $replyToDetail = null;
        if ($this->repliedMessage) {
            $replyToDetail = $this->repliedMessage;
            if (is_string($replyToDetail->quoted_message)) {
                $replyToDetail->quoted_message = json_decode($replyToDetail->quoted_message, true);
            }
            $replyId        = $replyToDetail->id;
            $replyType      = $replyToDetail->type;
            $replyMediaUrl  = config('app.url') . '/' . $replyToDetail->file_path;
            $replyTo        = $replyToDetail->from == 'device' ? 'Anda' : ($replyToDetail->historyName->name ?? '');
        }

        return [
            'merchant_id'           => $this->history->merchant_id ?? $this->history->business_id ?? null,
            'conversation_id'       => $this->history_chat_id,
            'created_at'            => $this->created_at,
            'detail'                => $replyToDetail,
            'datetime'              => array(
                'date'                  => $this->created_at->format('d/m/Y'),
                'time'                  => $this->created_at->format('H:i')
            ),
            'user'                  => array(
                'id'                    => $this->reply->id ?? null,
                'name'                  => $this->reply->name ?? null
            ),
            'id'                    => $this->id,
            'media_type'            => $this->type,
            'media_size'            => (int)$this->file_size,
            'media_url'             => !empty($this->file_path) ? asset($this->file_path) : null,
            'original_name'         => $this->original_name,
            'message'               => $this->message,
            'from'                  => $this->from, // FIX: tambah field 'from' agar frontend bisa cek
            'sent_by'               => $this->from == 'user' ? $this->history_chat_id : 'system',
            'sent_by_name'          => $this->from == 'user' ? ($this->history->name ?? '') : ($this->reply->name ?? 'system'),
            'status'                => 'sent',
            'reply_id'              => $replyId,
            'reply_text'            => $this->reply_text,
            'reply_to'              => $replyTo,
            'reply_type'            => $replyType,
            'reply_media_url'       => $replyMediaUrl,
            'reactions'             => $this->reactions ? $this->reactions->map(function($r) {
                return [
                    'emoji' => $r->emoji,
                    'from'  => $r->reactor_phone,
                    'type'  => $r->reactor_type,
                ];
            })->values()->toArray() : [],
        ];
    }
}
