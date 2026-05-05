<?php

namespace App\Models;

use App\Models\Merchant\Merchant;
use App\Models\Package\PackageTransaction;
use App\Models\Scopes\FilterByMerchantScope;
use App\Observers\Saas\Saas\MultiBusinessObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'gmap_key',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_encryption',
        'mail_from_name',
        'whatsapp_sender_notif',
        'timezone',
        'scrapp_phone',
        'scrapp_phone_whatsapp',
        'phone_country_code',
        'default_lang',
        'open_ai_key',
        'local_api_key',
        'merchant_id',
        'scrapp_counter',
        'whatsapp_sender',
        'email_sender',
        'api_device_use',
        'ai_option',
        'google_text_to_audio',
        'history_ai_chat_option',
        'history_ai_chat',
        'is_online',
        'cek_ongkir_option_api',
        'cek_ongkir_api',
        'ongkir_method',
        'google_code',
        'signature_option',
        'signature_text'
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
        static::addGlobalScope(new FilterByMerchantScope);
        Setting::observe(MultiBusinessObserver::class);

        static::creating(function ($model) {
            $model->id  = Uuid::uuid4()->toString();
            $user       = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
            }
        });
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function package_transaction()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'package')->orderBy("created_at", "desc");
    }

    public function transaction()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'package')->where("status", "success")->orderBy("created_at", "desc");
    }

    public function package_active()
    {
        return $this->hasOne(PackageTransaction::class, 'business_id')->where('type', 'package')->where("status", "success")->where(function ($q) {
            $q->where("expire_date", ">=", now())->orWhere('days_option', 'unlimited');
        })->orderBy("created_at", "desc");
    }

    public function package_active_storage()
    {
        return $this->hasOne(PackageTransaction::class, 'business_id')->where('type', 'storage')->where("status", "success")->where(function ($q) {
            $q->where("expire_date", ">=", now())->orWhere('days_option', 'unlimited');
        })->orderBy("created_at", "desc");
    }

    public function package_transaction_storage_pending()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'storage')->where('status', 'pending')->orderBy("created_at", "desc");
    }

    public function package_transaction_topup()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'topup')->orderBy("created_at", "desc");
    }

    public function package_transaction_topup_pending()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'topup')->where('status', 'pending')->orderBy("created_at", "desc");
    }

    public function package_transaction_mua_pending()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'mua')->where('status', 'pending')->orderBy("created_at", "desc");
    }

    public function transaction_topup()
    {
        return $this->hasMany(PackageTransaction::class, 'business_id')->where('type', 'topup')->where("status", "success")->orderBy("created_at", "desc");
    }

    public function package_active_topup()
    {
        return $this->hasOne(PackageTransaction::class, 'business_id')->where('type', 'topup')->where("status", "success")->where("ai_response", ">", 'using_credit_limit')->orderBy("created_at", "desc");
    }

    public function package_active_mua()
    {
        return $this->hasOne(PackageTransaction::class, 'business_id')->where('type', 'mua')->where("status", "success")->where("ai_response", ">", 'using_credit_limit')->orderBy("created_at", "desc");
    }

    public function getTransactionPackagePendingAttribute()
    {
        $status = true;

        if (count($this->package_transaction->where("status", "pending")) > 0) {
            $status = false;
        }

        return $status;
    }

    public function getProgressDayAttribute()
    {
        if ($this->package_active) {
            $expireDate     = Carbon::parse($this->package_active->expire_date);
            $startDate      = Carbon::parse($this->package_active->created_at);
            $totalDays      = $startDate->diffInDays($expireDate);
            $elapsedDays    = Carbon::now()->diffInDays($startDate);
            $progress = $totalDays > 0 ? (($elapsedDays / $totalDays) * 100) : 0;
            return round(abs($progress), 2);
        } else {
            return 0;
        }
    }

    public function getRemainingDayAttribute()
    {
        if ($this->package_active) {
            $expireDate     = Carbon::parse($this->package_active->expire_date);
            $now           = Carbon::now();

            // Hitung remaining days dengan memastikan tidak negatif
            $remainingDays = $now->lessThan($expireDate) ? $now->diffInDays($expireDate) : 0;

            return $remainingDays;
        } else {
            return 0;
        }
    }
}
