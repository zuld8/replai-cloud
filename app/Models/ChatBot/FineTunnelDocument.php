<?php

namespace App\Models\ChatBot;

use App\Models\Scopes\FilterByBusinessScope;
use App\Observers\ChatBot\FineTunnelDocumentObserver;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineTunnelDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fine_tunnel_id',
        'filename',
        'file_path',
        'file_type',
        'file_size',
        'total_chunks',
        'status',
        'error_message',
        'merchant_id',
        'business_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);
        FineTunnelDocument::observe(FineTunnelDocumentObserver::class);

        static::creating(function ($model) {
            $user = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    public function fineTunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    public function chunks()
    {
        return $this->hasMany(FineTunnelDocumentChunk::class, 'document_id');
    }
}
