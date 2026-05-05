<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WabaAgent extends Pivot
{
    use HasUuids;

    protected $fillable = ['waba_id', 'user_id'];
    public $incrementing = false;
    protected $keyType = 'string';
}
