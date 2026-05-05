<?php
namespace App\Models\Affiliate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffiliateWithdrawal extends Model
{
    protected $table = 'affiliate_withdrawals';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','affiliate_id','amount','bank_name',
        'bank_account','bank_holder','status','notes','paid_at'
    ];

    protected $casts = ['paid_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) { if (!$m->id) $m->id = (string) Str::uuid(); });
    }

    public function affiliate() { return $this->belongsTo(Affiliate::class); }
}
