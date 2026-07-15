<?php

namespace App\Models\ChatFlow;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\FilterByBusinessScope;

class ChatFlow extends Model
{
    protected $table        = 'chat_flows';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'business_id', 'merchant_id', 'name',
        'trigger_type', 'trigger_keywords', 'keyword_match', 'channels',
        'start_node_id', 'fallback_action', 'session_timeout_min', 'status',
    ];

    protected $casts = [
        'trigger_keywords' => 'array',
        'channels'         => 'array',
    ];

    protected static function booted(): void
    {
        // GlobalScope aktif di context WEB (admin login). Di webhook (my_business=null) jadi no-op.
        // Engine WAJIB pakai where('business_id', ...) eksplisit — lihat ChatFlowEngine.
        static::addGlobalScope(new FilterByBusinessScope);

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
            }
            // Guard: hanya set dari auth jika belum di-set eksplisit
            if (empty($model->business_id) && my_business()) {
                $model->business_id = my_business();
                $model->merchant_id = auth()->user()->merchant_id ?? null;
            }
        });
    }

    public function nodes()
    {
        return $this->hasMany(ChatFlowNode::class, 'flow_id')->orderBy('position');
    }

    public function startNode()
    {
        return $this->belongsTo(ChatFlowNode::class, 'start_node_id');
    }
}
