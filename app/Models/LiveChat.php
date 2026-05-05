<?php

namespace App\Models;

use App\Models\Agent\LiveChatAgent;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class LiveChat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'description',
        'agent',
        'type',
        'finetunnel_id',
        'merchant_id',
        'business_id',
        'photo',
        'faqs',
        'sosmed'
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
            $model->id  = Uuid::uuid4()->toString();
            $user       = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    public function getTypeNameAttribute()
    {
        if ($this->type == 'all') {
            return 'Ai dan ChatBot';
        }

        if ($this->type == 'chatbot') {
            return 'Chatbot';
        }

        if ($this->type == 'ai') {
            return 'Ai Training';
        }
    }

    public function finetunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'finetunnel_id');
    }

    public function history()
    {
        return $this->hasMany(HistoryChat::class, 'livechat_id');
    }

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'live_chat_agents', 'livechat_id', 'user_id')
            ->using(LiveChatAgent::class)
            ->withTimestamps();
    }


    public function getImageDataAttribute()
    {
        if (file_exists($this->photo)) {
            return $this->photo;
        } else {
            return 'images/user.png';
        }
    }
}
