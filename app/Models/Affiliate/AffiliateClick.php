<?php
namespace App\Models\Affiliate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AffiliateClick extends Model
{
    protected $table = 'affiliate_clicks';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','affiliate_id','ip_address','user_agent','created_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->id) $m->id = (string) Str::uuid();
            if (!$m->created_at) $m->created_at = now();
        });
    }
}
