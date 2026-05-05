<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class MediaContent extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'format',
        'name',
        'path',
        'folder_id'
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

    public function folder()
    {
        return $this->belongsTo(Folder::class,'folder_id');
    }  
}
