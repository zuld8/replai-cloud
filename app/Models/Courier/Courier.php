<?php

namespace App\Models\Courier;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Courier extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'name',
        'code',
        'logo',
        'status',
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
