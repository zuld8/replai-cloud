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

class InstagramAccount extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'business_id',
        'instagram_id',
        'agent',
        'username',
        'name',
        'profile_picture_url',
        'biography',
        'website',
        'followers_count',
        'follows_count',
        'media_count',
        'page_id',
        'page_name',
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
        'follows_count' => 'integer',
        'media_count' => 'integer',
    ];

    protected $hidden = [
        'access_token',
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
        static::addGlobalScope(new FilterByBusinessScope);
        static::creating(function ($model) {
            $user       = my_user();
            if ($user) {
                $model->business_id = my_business();
            }
        });
    }


    public function finetunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function messages()
    {
        return $this->hasMany(HistoryChat::class, 'instagram_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessors & Mutators
     */
    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = encrypt($value);
    }

    public function getAccessTokenAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Helper Methods
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTokenExpired()
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }

    public function markAsActive()
    {
        $this->update([
            'status' => 'active',
            'error_message' => null,
        ]);
    }

    public function markAsError($errorMessage)
    {
        $this->update([
            'status' => 'error',
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }


    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute()
    {
        return 0;
    }

    /**
     * Get total conversations count
     */
    public function getTotalConversationsCountAttribute()
    {
        return $this->messages()->count();
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'instagram_agents', 'instagram_id', 'user_id')
            ->withTimestamps();
    }
}
