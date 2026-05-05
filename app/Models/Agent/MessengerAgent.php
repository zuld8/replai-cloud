<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class MessengerAgent extends Pivot
{
    protected $fillable = ['id', 'messenger_id', 'user_id'];
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Boot the model - generate UUID for id on creating
     * Note: HasUuids trait does NOT work on Pivot models when used via sync/attach
     * because those bypass Eloquent's creating event. We use newInstance instead.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Override newInstance to ensure UUID is set even when Pivot is
     * created via attach/sync (which bypass creating event in some versions)
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $instance = parent::newInstance($attributes, $exists);

        if (!$exists && empty($instance->id)) {
            $instance->id = (string) Str::uuid();
        }

        return $instance;
    }
}
