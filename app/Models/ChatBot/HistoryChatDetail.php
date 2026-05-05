<?php

namespace App\Models\ChatBot;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class HistoryChatDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'history_chat_id',
        'from',
        'message',
        'reply_by_id',
        'file_path',
        'file_type',
        'file_size',
        'type',
        'credit_using',
        'remotejid',
        'messageid',
        'is_follow_up',
        'follow_up_id',
        'is_read',
        'reply_to',
        'reply_text',
        'original_name',
        'quoted_message'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded          = ['id'];
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    public $incrementing        = false;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });

        static::created(function ($model) {
            // Increment unread_count on parent history_chat when a USER message arrives
            // This counter is used for badge display in CRM contact list
            // Reset happens in CrmController when agent opens/reads the chat
            if ($model->from === 'user' && $model->history_chat_id) {
                \App\Models\ChatBot\HistoryChat::where('id', $model->history_chat_id)
                    ->increment('unread_count');
            }
        });
    }

    // tambahkan cast kolom quoted_message adalah json
    protected $casts = [
        'quoted_message' => 'json',
    ];

    public function history()
    {
        return $this->belongsTo(HistoryChat::class, 'history_chat_id');
    }

    public function reply()
    {
        return $this->belongsTo(User::class, 'reply_by_id');
    }

    public function historyName()
    {
        return $this->belongsTo(HistoryChat::class, 'history_chat_id')->select('id', 'name');
    }

    public function repliedMessage()
    {
        return $this->belongsTo(HistoryChatDetail::class, 'reply_to', 'id');
    }

    /**
     * Get reactions for this message
     */
    public function reactions()
    {
        return $this->hasMany(MessageReaction::class, 'history_chat_detail_id');
    }
}
