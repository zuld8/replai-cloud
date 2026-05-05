<?php

namespace App\Models;

use App\Models\Agent\WabaAgent;
use App\Models\ChatBot\FineTunnel;
use App\Models\Master\MessageTemplate;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class WhatsappKeyAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
        'reply_any_chat',
        'reply_method',
        'template_id',
        'reply_text',
        'auto_reply_option',
        'merchant_id',
        'meta_data',
        'callback_token',
        'agent',
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

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }


    public function templates()
    {
        return $this->hasMany(MessageTemplate::class, 'waba_device_id');
    }

    public function finetunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    public function meta()
    {
        return $this->belongsTo(MetaAccount::class, 'meta_account_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'waba_agents', 'waba_id', 'user_id')
            ->using(WabaAgent::class)
            ->withTimestamps();
    }
    public function metaAccount()
    {
        return $this->belongsTo(\App\Models\MetaAccount::class, 'meta_account_id');
    }

}