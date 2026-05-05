<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class FooterLink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'position',
        'name',
        'order_position'
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

    public function getPositionNameAttribute()
    {
        if($this->position == 1) {
            return 'Header Menu';
        }

        if($this->position == 2) {
            return 'Footer 1';
        }

        if($this->position == 3) {
            return 'Footer 2';
        }

        if($this->position == 4) {
            return 'Footer 3';
        }

        return '';
    }
}
