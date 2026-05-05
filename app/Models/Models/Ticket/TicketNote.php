<?php

namespace App\Models\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Ticket\Ticket;
use App\Models\User;

class TicketNote extends Model
{
    use HasUuids;

    protected $table = 'ticket_notes';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'note'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship: Ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    /**
     * Relationship: User who created the note
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
