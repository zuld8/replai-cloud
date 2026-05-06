<?php

namespace App\Models\Flow;

use Illuminate\Database\Eloquent\Model;
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

    protected $casts = [
        'payment_accounts' => 'array',
    ];

    protected $guarded  = ['id'];
    protected $primaryKey = 'id';
    protected $keyType  = 'string';
    public $incrementing = false;
}
