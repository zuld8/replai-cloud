<?php

namespace App\Models\Ticket;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TicketAgent extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ticket_id',
        'agent_id',
        'role',
        'assigned_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'assigned_at' => 'datetime',
    ];


    /**
     * The attributes that are guarded.
     */
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
