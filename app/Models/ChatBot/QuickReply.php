<?php

namespace App\Models\ChatBot;

use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class QuickReply extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'merchant_id',
        'merchant_id',
        'name',
        'content',
        'type',
        'file_type',
        'file_name',
        'file_size',
        'media_url'
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
            $model->id  = Uuid::uuid4()->toString();
            if (my_business()) {
                $model->business_id = my_business();
            }
        });
    }

}
