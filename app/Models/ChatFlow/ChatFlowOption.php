<?php

namespace App\Models\ChatFlow;

use Illuminate\Database\Eloquent\Model;

class ChatFlowOption extends Model
{
    protected $table        = 'chat_flow_options';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'node_id', 'kind', 'label', 'description', 'section',
        'order', 'target_action', 'target_node_id', 'reply_id',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
            }
        });
    }

    public function node()
    {
        return $this->belongsTo(ChatFlowNode::class, 'node_id');
    }

    public function targetNode()
    {
        return $this->belongsTo(ChatFlowNode::class, 'target_node_id');
    }
}
