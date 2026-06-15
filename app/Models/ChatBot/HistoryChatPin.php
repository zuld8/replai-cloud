<?php

namespace App\Models\ChatBot;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HistoryChatPin extends Model
{
    protected $table    = 'history_chat_pins';
    public $incrementing = false;

    protected $fillable = ['history_chat_id', 'user_id'];

    protected $primaryKey = null; // composite PK, no single PK
    public $timestamps = true;

    public function history(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HistoryChat::class, 'history_chat_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
