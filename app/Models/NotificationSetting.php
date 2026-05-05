<?php

namespace App\Models;

use App\Models\Master\MessageTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class NotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'device_notification',
        'waba_device_notification',
        'received_email_notification',
        'whatsapp_register',
        'whatsapp_register_template',
        'whatsapp_buy_package',
        'whatsapp_buy_package_template',
        'whatsapp_package_payment',
        'whatsapp_package_payment_template',
        'whatsapp_package_user',
        'whatsapp_package_user_template',
        'whatsapp_approval_payment',
        'whatsapp_approval_payment_template',
        'email_register',
        'email_register_template',
        'email_buy_package',
        'email_buy_package_template',
        'email_package_payment',
        'email_package_payment_template',
        'email_package_user',
        'email_package_user_template',
        'email_approval_payment',
        'email_approval_payment_template',
        'received_notification'
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
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'device_notification');
    }

    public function wabaDevice()
    {
        return $this->belongsTo(\App\Models\WhatsappKeyAccount::class, 'waba_device_notification');
    }

    public function register_template_whatsapp()
    {
        return $this->belongsTo(MessageTemplate::class, 'whatsapp_register_template');
    }

    public function buy_package_template_whatsapp()
    {
        return $this->belongsTo(MessageTemplate::class, 'whatsapp_buy_package_template');
    }

    public function package_payment_template_whatsapp()
    {
        return $this->belongsTo(MessageTemplate::class, 'whatsapp_package_payment_template');
    }

    public function package_user_template_whatsapp()
    {
        return $this->belongsTo(MessageTemplate::class, 'whatsapp_package_user_template');
    }

    public function approval_payment_template_whatsapp()
    {
        return $this->belongsTo(MessageTemplate::class, 'whatsapp_approval_payment_template');
    }

    public function register_template_email()
    {
        return $this->belongsTo(MessageTemplate::class, 'email_register_template');
    }

    public function buy_package_template_email()
    {
        return $this->belongsTo(MessageTemplate::class, 'email_buy_package_template');
    }

    public function package_payment_template_email()
    {
        return $this->belongsTo(MessageTemplate::class, 'email_package_payment_template');
    }

    public function package_user_template_email()
    {
        return $this->belongsTo(MessageTemplate::class, 'email_package_user_template');
    }

    public function approval_payment_template_email()
    {
        return $this->belongsTo(MessageTemplate::class, 'email_approval_payment_template');
    }
}
