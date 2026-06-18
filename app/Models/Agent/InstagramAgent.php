<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InstagramAgent extends Pivot
{
    use HasUuids;

    protected $table = 'instagram_agents'; // Pivot does NOT auto-pluralize, must set explicitly

    protected $fillable = ['id', 'instagram_id', 'user_id'];
    public $incrementing = false;
    protected $keyType = 'string';
}
