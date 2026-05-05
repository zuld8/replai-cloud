<?php
namespace App\Models\Affiliate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffiliateCommission extends Model
{
    protected $table = 'affiliate_commissions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','affiliate_id','referred_user_id','transaction_id',
        'package_name','transaction_amount','commission_rate',
        'commission_month','amount','status','available_at'
    ];

    protected $casts = ['available_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->id) $m->id = (string) Str::uuid(); });
    }

    public function affiliate() { return $this->belongsTo(Affiliate::class); }
    public function referredUser() { return $this->belongsTo(\App\Models\User::class, 'referred_user_id'); }
}
