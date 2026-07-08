<?php

namespace App\Models\ChatBot;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class HistoryChatDetail extends Model
{
    use SoftDeletes;

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
        'source',
        'buttons',
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
        'quoted_message',
        'extra',
        'msg_category',
        'billable',
        'pricing_model',
        'conversation_id',
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
            // Saat pesan masuk dari pelanggan (from='user'):
            // 1. Increment unread_count (badge di CRM sidebar)
            // 2. Update last_inbound_at (untuk chip sesi 24 jam WABA)
            // Keduanya dalam 1 query untuk efisiensi.
            if ($model->from === 'user' && $model->history_chat_id) {
                \App\Models\ChatBot\HistoryChat::where('id', $model->history_chat_id)
                    ->update([
                        'unread_count'    => \Illuminate\Support\Facades\DB::raw('unread_count + 1'),
                        'last_inbound_at' => now(),  // reset 24h session window WABA
                    ]);
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
