<?php

namespace App\Models\Master;

use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineSegment extends Model
{

    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'position'
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
                $model->business_id = my_business();
            }
        });
    }

    public function labels()
    {
        return $this->hasMany(Label::class, 'pipeline_segment_id');
    } 
}
