<?php

namespace App\Models\Meta;

use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessengerAccount extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'business_id',
        'page_id',
        'agent',
        'page_name',
        'page_username',
        'page_picture_url',
        'category',
        'about',
        'phone',
        'email',
        'website',
        'followers_count',
        'access_token',
        'token_expires_at',
        'status',
        'error_message',
        'details',
        'auto_reply_method',
        'fine_tunnel_id',
        'limit_per_day',
        'daily_send',
        'daily_limit',
        'daily_date',
        'auto_reply_certain_day',
        'days',
        'auto_reply_certain_time',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'details' => 'array',
        'token_expires_at' => 'datetime',
        'followers_count' => 'integer',
        'daily_date' => 'date',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);
        static::creating(function ($model) {
            $user = my_user();
            if ($user) {
                $model->business_id = my_business();
            }
        });
    }

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function finetunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    public function histories()
    {
        return $this->hasMany(HistoryChat::class, 'messanger_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'messenger_agents', 'messenger_id', 'user_id')
            ->withTimestamps();
    }


    /**
     * Reset daily send count if needed
     */
    public function resetDailySendIfNeeded()
    {
        if ($this->daily_date != now()->format('Y-m-d')) {
            $this->update([
                'daily_send' => 0,
                'daily_date' => now()->format('Y-m-d')
            ]);
        }
    }

    /**
     * Increment daily send count
     */
    public function incrementDailySend()
    {
        $this->resetDailySendIfNeeded();
        $this->increment('daily_send');
    }

    /**
     * Check if can send message
     */
    public function canSendMessage(): bool
    {
        if ($this->daily_limit !== 'yes') {
            return true;
        }

        $this->resetDailySendIfNeeded();
        return $this->daily_send < $this->limit_per_day;
    }
}
