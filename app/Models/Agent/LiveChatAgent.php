<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LiveChatAgent extends Pivot
{
    use HasUuids;

    protected $table = 'live_chat_agents';

    protected $fillable = ['livechat_id', 'user_id'];
    public $incrementing = false;
    protected $keyType = 'string';
}
