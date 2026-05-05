<?php

namespace App\Models\Courier;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class CourierFineTunnel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'finetunnel_id',
        'name',
        'code',  
        'service'
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
            $model->id = Uuid::uuid4()->toString();
        });
    }
}
