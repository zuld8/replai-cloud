<?php

namespace App\Models;

use App\Models\Agent\DeviceAgent;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\Master\MessageTemplate;
use App\Models\Scopes\FilterByBusinessScope;
use App\Observers\WhatsappDeviceObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class WhatsappDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'whatsapp_key',
        'whatsapp_session',
        'limit_per_day',
        'daily_send',
        'status',
        'auto_reply_method',
        'fine_tunnel_id',
        'daily_limit',
        'auto_reply_certain_day',
        'days',
        'auto_reply_certain_time',
        'start_time',
        'end_time',
        'webhook',
        'auto_read_before_autorespon',
        'auto_read_in_chattapp',
        'reply_any_chat',
        'reply_method',
        'template_id',
        'reply_text',
        'phone_notification',
        'chat_history',
        'auto_reply_option',
        'agent'
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

        WhatsappDevice::observe(WhatsappDeviceObserver::class);

        // static::addGlobalScope(new FilterByMerchantScope);
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

    public function finetunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function histories()
    {
        return $this->hasMany(HistoryChat::class, 'device_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'device_agents', 'device_id', 'user_id')
            ->using(DeviceAgent::class)
            ->withTimestamps();
    }
}
