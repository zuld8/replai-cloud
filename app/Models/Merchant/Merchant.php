<?php

namespace App\Models\Merchant;

use App\Models\Package\PackageTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Observers\Saas\MerchantObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Merchant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'merchant_category_id',
        'owner_id',
        'address',
        'zip_code',
        'status'
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
        Merchant::observe(MerchantObserver::class);
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->orderBy('created_at', 'asc')->withoutGlobalScopes();
    }

    public function category()
    {
        return $this->belongsTo(MerchantCategory::class, 'merchant_category_id');
    }

    public function package_transaction()
    {
        return $this->hasMany(PackageTransaction::class, 'merchant_id')->where('type', 'package')->orderBy("created_at", "desc");
    }

    public function transaction()
    {
        return $this->hasMany(PackageTransaction::class, 'merchant_id')->where("status", "success")->orderBy("created_at", "desc");
    }

    public function package_active()
    {
        return $this->hasOne(PackageTransaction::class, 'merchant_id')->where("status", "success")->where(function ($q) {
            $q->where("expire_date", ">=", now())->orWhere('days_option', 'unlimited');
        })->orderBy("created_at", "desc");
    }

    public function business()
    {
        return $this->hasMany(Setting::class, 'merchant_id');
    }

    public function getTransactionPackagePendingAttribute()
    {
        $status = true;

        if (count($this->package_transaction->where("status", "pending")) > 0) {
            $status = false;
        }

        return $status;
    }

    public function getUserLimitAttribute()
    {
        $limitation      = $this->transaction()->where(function ($q) {
            $q->where("expire_date", ">=", now())->orWhere('days_option', 'unlimited');
        })->where('limit_user_option', 'no')->first(['id']);

        if ($limitation) {
            return 'unlimited';
        }

        $limitation     =  $this->transaction()->where(function ($q) {
            $q->where("expire_date", ">=", now())->orWhere('days_option', 'unlimited');
        })->sum('users_limit');

        return (int)$limitation;
    }
}
