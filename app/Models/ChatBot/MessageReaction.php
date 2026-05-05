<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Model;

class MessageReaction extends Model
{
    protected $table = 'message_reactions';

    protected $fillable = [
        'history_chat_detail_id',
        'message_wamid',
        'emoji',
        'reactor_phone',
        'reactor_type',
    ];

    public function detail()
    {
        return $this->belongsTo(HistoryChatDetail::class, 'history_chat_detail_id');
    }
}
