<?php

namespace App\Models\ChatBot;

use App\Models\Master\MessageTemplate;
use App\Models\Scopes\FilterByBusinessScope;
use App\Observers\ChatBot\ChatBotObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ChatBot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'keyword',
        'select_device',
        'select_telegram',
        'reply_method',
        'template_id',
        'message',
        'select_livechat',
        'whatsapp_waba_id',
        'select_instagram',
        'select_messanger',
        'metadata',
        'file',
        'meta_account_id'
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
        ChatBot::observe(ChatBotObserver::class);
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

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    public function details()
    {
        return $this->hasMany(ChatBotImage::class, 'chatbot_id');
    }
}
