<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
   public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'guard_name', 'description'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function modulFitures(): BelongsToMany
    {
        return $this->belongsToMany(
            ModulFiture::class,
            'permission_modul_fiture',
            'permission_id',
            'modul_fiture_id'
        );
    }
}