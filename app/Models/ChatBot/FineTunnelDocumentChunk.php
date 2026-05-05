<?php

namespace App\Models\ChatBot;

use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineTunnelDocumentChunk extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'document_id',
        'content',
        'image_path',
        'metadata',
        'chunk_index',
        'token_count',
        'embedding',
        'merchant_id',
        'business_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'embedding' => 'array'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);

        static::creating(function ($model) {
            $user = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo(FineTunnelDocument::class, 'document_id');
    }

    /**
     * Calculate cosine similarity with another embedding
     */
    public function cosineSimilarity(array $otherEmbedding): float
    {
        $embedding = $this->embedding;

        if (!$embedding || !$otherEmbedding) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($embedding); $i++) {
            $dotProduct += $embedding[$i] * $otherEmbedding[$i];
            $normA += $embedding[$i] * $embedding[$i];
            $normB += $otherEmbedding[$i] * $otherEmbedding[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        return asset($this->image_path);
    }
}
