<?php

namespace App\Models\ChatFlow;

use Illuminate\Database\Eloquent\Model;

class ChatFlowNode extends Model
{
    protected $table        = 'chat_flow_nodes';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'flow_id', 'type', 'body_text',
        'header', 'footer', 'list_button_label',
        'handoff_assign_to', 'position',
    ];

    protected static function booted(): void
    {
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

    public function options()
    {
        return $this->hasMany(ChatFlowOption::class, 'node_id')->orderBy('order');
    }
}
