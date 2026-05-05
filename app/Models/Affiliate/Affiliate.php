<?php
namespace App\Models\Affiliate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $table = 'affiliates';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','user_id','code','total_click','total_referral',
        'total_active','total_earned','total_withdrawn','status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function commissions() { return $this->hasMany(AffiliateCommission::class); }
    public function withdrawals() { return $this->hasMany(AffiliateWithdrawal::class); }

    public function getAvailableBalance()
    {
        return $this->commissions()->where('status','available')->sum('amount');
    }

    public function getPendingBalance()
    {
        return $this->commissions()->where('status','pending')->sum('amount');
    }
}
