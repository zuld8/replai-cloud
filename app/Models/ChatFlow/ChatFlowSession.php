<?php

namespace App\Models\ChatFlow;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\FilterByBusinessScope;

class ChatFlowSession extends Model
{
    protected $table        = 'chat_flow_sessions';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'business_id', 'history_chat_id',
        'flow_id', 'current_node_id', 'status', 'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // GlobalScope aktif di WEB. Di webhook (no auth) → no-op.
        // ⚠️ WAJIB: Saat create di webhook, set business_id eksplisit dari $histories->business_id
        static::addGlobalScope(new FilterByBusinessScope);

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
            }
        });
    }

    public function flow()
    {
        return $this->belongsTo(ChatFlow::class, 'flow_id');
    }

    public function currentNode()
    {
        return $this->belongsTo(ChatFlowNode::class, 'current_node_id');
    }
}
