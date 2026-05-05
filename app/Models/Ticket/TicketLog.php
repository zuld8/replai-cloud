<?php

namespace App\Models\Ticket;

use App\Models\Ticket\Ticket;
use App\Models\User;
use App\Models\Master\Label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TicketLog extends Model
{
    use HasFactory, HasUuids;
 

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ticket_id',
        'agent_id',
        'label_id',
        'log_time',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that are guarded.
     */
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Get the ticket associated with the log.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    /**
     * Get the agent (user) associated with the log.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }

    /**
     * Get the label associated with the log.
     */
    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id', 'id');
    }

    /**
     * Boot function to generate UUIDs
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

}