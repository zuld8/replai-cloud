<?php

namespace App\Models\Package;

use App\Models\Merchant\Merchant;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PackageTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'merchant_id',
        'package_id',
        'price',
        'expire_date',
        'tax',
        'other_charge',
        'final_total',
        'add_days',
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
        'status',
        'ref_no',
        'invoice',
        'business_id',
        'ai_response',
        'livechat_limit',
        'limit_livechat',
        'using_credit_limit',
        'type',
        'new_order_ai_response',
        'days_option',
        'google_sheet',
        'cek_ongkir',
        'limit_instagram',
        'instagram',
        'limit_messanger',
        'messanger',
        'limit_telegram',
        'telegram',
        'storage',
        'mua_limit',
        'mua_limit_optin',
        'new_order_mua_limit',
        'using_mua_limit',
        'max_per_upload',
        'max_total_rag'
    ];

    protected $dates = [
        'expire_date',
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

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id')->withoutGlobalScopes();
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function payment()
    {
        return $this->hasOne(PackageTransactionPayment::class, 'package_transaction_id');
    }

    public function getSisaCreditAttribute()
    {
        $sisa   = $this->ai_response - $this->using_credit_limit;
        if ($sisa > 0) {
            return (int)$sisa;
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
