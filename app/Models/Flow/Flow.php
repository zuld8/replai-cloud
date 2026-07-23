<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Flow extends Model
{
    use HasUuids;

    protected $table = 'flows';

    protected $fillable = [
        'business_id',
        'merchant_id',
        'name',
        'keyword',
        'flow_type',
        'select_device',
        'select_waba',
        'select_telegram',
        'select_livechat',
        'qris_image',
        'payment_accounts',
        'message_open',
        'message_close',
        'status',
    ];

    protected static function booted(): void
    {
        // Isolasi antar-tenant — Flow hanya bisa diakses oleh business yang membuatnya
        static::addGlobalScope(new FilterByBusinessScope);
    }



    protected $casts = [
        'payment_accounts' => 'array',
    ];

    protected $guarded  = ['id'];
    protected $primaryKey = 'id';
    protected $keyType  = 'string';
    public $incrementing = false;
}
