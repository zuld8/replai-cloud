<?php

namespace App\Models\Master;

use App\Models\ChatBot\HistoryChat;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Store\Store;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tag',
        'position',
        'color',
        'type', // CRM / ticket / tags
        'is_default', // yes / no
        'pipeline_segment_id',
        'is_closeable',
        'is_default'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded          = ['id'];
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    public $incrementing        = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);
        static::creating(function ($model) {
            $user       = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    public function chats()
    {
        return HistoryChat::whereJsonContains('label', ['id' => $this->id]);
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'label_id');
    }

    public function pipeline()
    {
        return $this->belongsTo(PipelineSegment::class, 'pipeline_segment_id');
    }


}
