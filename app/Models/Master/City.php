<?php

namespace App\Models\Master;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'province_id',
        'code',
        'status',
        'id'
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
        static::creating(function ($model) {
            $model->id  = Uuid::uuid4()->toString(); 
        });
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'city_id');
    }
}
