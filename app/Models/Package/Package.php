<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'price',
        'add_days',
        'trial_version',
        'limit_user_option',
        'users_limit',
        'limit_whatsapp_option',
        'limit_whatsapp_priode',
        'whatsapp_limit',
        'limit_email_option',
        'limit_email_priode',
        'email_limit',
        'limit_scrapp_option',
        'limit_scrapp_priode',
        'scrapp_limit',
        'limit_device',
        'device_limit',
        'limit_template',
        'template_limit',
        'limit_ai_training',
        'ai_training_limit',
        'limit_chatbot',
        'chatbot_limit',
        'ai_response',
        'livechat_limit',
        'limit_livechat',
        'days_option',
        'cek_ongkir',
        'google_sheet',
        'limit_instagram',
        'instagram',
        'limit_messanger',
        'limit_waba',
        'waba_limit',
        'messanger',
        'limit_telegram',
        'telegram',
        'storage',
        'type',
        'mua_limit',
        'mua_limit_optin',
        'max_per_upload',
        'max_total_rag'
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

    public function details()
    {
        return $this->hasMany(PackageDetail::class, 'package_id');
    }

    public function getWhatsappPriodeAttribute()
    {
        if ($this->limit_whatsapp_option == 'yes') {
            if ($this->limit_whatsapp_priode == 'daily') {
                return __('daily');
            }

            if ($this->limit_whatsapp_priode == 'monthly') {
                return __('monthly');
            }

            if ($this->limit_whatsapp_priode == 'yearly') {
                return __('yearly');
            }
        }

        return '';
    }

    public function getEmailPriodeAttribute()
    {
        if ($this->limit_email_option == 'yes') {
            if ($this->limit_email_priode == 'daily') {
                return __('daily');
            }

            if ($this->limit_email_priode == 'monthly') {
                return __('monthly');
            }

            if ($this->limit_email_priode == 'yearly') {
                return __('yearly');
            }
        }

        return '';
    }

    public function getScrappingPriodeAttribute()
    {
        if ($this->limit_scrapp_option == 'yes') {
            if ($this->limit_scrapp_priode == 'daily') {
                return __('daily');
            }

            if ($this->limit_scrapp_priode == 'monthly') {
                return __('monthly');
            }

            if ($this->limit_scrapp_priode == 'yearly') {
                return __('yearly');
            }
        }

        return '';
    }

    public function getPricingDailyAttribute()
    {
        if ($this->add_days > 0 && $this->price > 0) {
            $price      = (float)ceil($this->price / $this->add_days);
            return $price;
        }

        return 0;
    }

    public function getStorageNameAttribute()
    {
        if ($this->storage < 1000) {
            return $this->storage . ' Mb';
        } else {
            return (float)($this->storage / 1000) . ' Gb';
        }
    }
}
