<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TelegramAgent extends Pivot
{
    use HasUuids;

    protected $fillable = ['telegram_id', 'user_id'];
    public $incrementing = false;
    protected $keyType = 'string';
}
