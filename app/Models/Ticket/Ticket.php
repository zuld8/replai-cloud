<?php

namespace App\Models\Ticket;

use App\Models\Store\Store;
use App\Models\Master\Label;
use App\Models\Models\Ticket\TicketNote;
use App\Models\User;
use App\Models\Ticket\TicketCategory;
use App\Models\Scopes\FilterByBusinessScope;
use App\Observers\Ticket\TicketObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Ticket extends Model
{
    use HasFactory, HasUuids;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contact_id',
        'label_id',
        'agent_id',
        'category_id',
        'business_id',
        'ticket_name',
        'ticket_level',
        'title',
        'notes',
        'file',
        'ticket_id',
        'create_time',
        'status',
        'priority',
        'resolved_at',
        'resolved_by',
        'assigned_at',
        'contact_name',
        'contact_email',
        'contact_phone'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'create_time' => 'datetime',
        'resolved_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /**
     * The attributes that are guarded.
     */
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);
        Ticket::observe(TicketObserver::class);

        static::creating(function ($model) {
            $model->business_id = my_business();
            if (empty($model->ticket_id)) {
                $model->ticket_id = static::generateTicketId();
            }
            if (empty($model->create_time)) {
                $model->create_time = now();
            }
        });
    }



    /**
     * Generate unique ticket ID with format: TICKET-YYYYMMDD-XXXX
     */
    public static function generateTicketId(): string
    {
        $date = now()->format('Ymd');
        $prefix = "TICKET-{$date}-";

        do {
            // Generate random 4-character alphanumeric string
            $suffix = strtoupper(Str::random(4));
            $ticketId = $prefix . $suffix;
        } while (static::where('ticket_id', $ticketId)->exists());

        return $ticketId;
    }

    /**
     * Relationship: Contact (Store)
     */
    public function contact()
    {
        return $this->belongsTo(Store::class, 'contact_id', 'id');
    }

    /**
     * Relationship: Label
     */
    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id', 'id');
    }

    /**
     * Relationship: Agent (User)
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }

    /**
     * Relationship: Agents (Multiple) - Many to Many
     */
    public function agents()
    {
        return $this->hasMany(TicketAgent::class, 'ticket_id');
    }

    /**
     * Relationship: Resolved By (User)
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by', 'id');
    }

    /**
     * Relationship: Category
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id', 'id');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by priority/level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('ticket_level', $level);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if ticket is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved' && !empty($this->resolved_at);
    }

    /**
     * Check if ticket is assigned
     */
    public function isAssigned(): bool
    {
        return !empty($this->agent_id);
    }

    /**
     * Get ticket age in hours
     */
    public function getAgeInHours(): int
    {
        return now()->diffInHours($this->created_at);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColor(): string
    {
        return match ($this->ticket_level) {
            'low' => '#28a745',
            'medium' => '#ffc107',
            'high' => '#dc3545',
            'urgent' => '#6f42c1',
            default => '#6c757d'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusClass(): string
    {
        return match ($this->status ?? 'open') {
            'open' => 'bg-primary',
            'in_progress' => 'bg-warning',
            'pending' => 'bg-info',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    public function ticketLogs()
    {
        return $this->hasMany(TicketLog::class, 'ticket_id', 'id');
    }

    /**
     * Relationship: Ticket Notes
     */
    public function notes()
    {
        return $this->hasMany(TicketNote::class, 'ticket_id', 'id')
            ->orderBy('created_at', 'desc');
    }
}
