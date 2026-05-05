<?php

namespace App\Observers\Ticket;

use App\Models\Models\Ticket\TicketNote;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketAgent;
use App\Models\Ticket\TicketLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    /**
     * Get tickets data with filters
     */
    public function getData(Request $request)
    {
        $query = Ticket::query();

        // Apply filters from the request
        if ($request->has('search') && $request->get('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('ticket_name', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('priority') && $request->get('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->has('ticket_level') && $request->get('ticket_level')) {
            $query->where('ticket_level', $request->get('ticket_level'));
        }

        if ($request->has('label_id') && $request->get('label_id')) {
            $query->where('label_id', $request->get('label_id'));
        }

        if ($request->has('category_id') && $request->get('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // NEW: Filter by human agent (support multiple agents via ticket_agents table)
        if ($request->has('human_agent_id') && $request->get('human_agent_id')) {
            $agentId = $request->get('human_agent_id');

            // Check both direct assignment and ticket_agents relationship
            $query->where(function ($q) use ($agentId) {
                $q->where('agent_id', $agentId) // Direct assignment
                    ->orWhereHas('agents', function ($subQuery) use ($agentId) {
                        $subQuery->where('agent_id', $agentId); // Multiple agents
                    });
            });
        }

        if ($request->has('contact_id') && $request->get('contact_id')) {
            $query->where('contact_id', $request->get('contact_id'));
        }

        // Date range filters
        if ($request->has('start_date') && $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }

        if ($request->has('end_date') && $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        // Resolved filter
        if ($request->has('is_resolved')) {
            $isResolved = $request->get('is_resolved');
            if ($isResolved === 'true' || $isResolved === true) {
                $query->where('status', 'resolved')->whereNotNull('resolved_at');
            } elseif ($isResolved === 'false' || $isResolved === false) {
                $query->where('status', '!=', 'resolved')->orWhereNull('resolved_at');
            }
        }

        // Assigned filter
        if ($request->has('is_assigned')) {
            $isAssigned = $request->get('is_assigned');
            if ($isAssigned === 'true' || $isAssigned === true) {
                $query->whereNotNull('agent_id');
            } elseif ($isAssigned === 'false' || $isAssigned === false) {
                $query->whereNull('agent_id');
            }
        }

        return $query;
    }

    /**
     * Create new ticket
     */
    public function createData(Request $request, $image = '')
    {

        return  Ticket::create([
            'contact_id'            => $request->contacts['id'],
            'label_id'              => $request->label_id,
            'category_id'           => $request->category_id,
            'ticket_name'           => $request->ticket_name,
            'ticket_level'          => $request->ticket_level,
            'title'                 => $request->title,
            'notes'                 => $request->notes,
            'file'                  => $image != "" ? $image : null,
            'status'                => 'open',
            'priority'              => $request->priority,
            'assigned_at'           => now()
        ]);
    }

    /**
     * Update existing ticket
     */
    public function updateData(Request $request, Ticket $ticket, $image = '')
    {

        $ticket->update([
            'contact_id'            => $request->contacts['id'],
            'label_id'              => $request->label_id,
            'category_id'           => $request->category_id,
            'ticket_name'           => $request->ticket_name,
            'ticket_level'          => $request->ticket_level,
            'title'                 => $request->title,
            'notes'                 => $request->notes,
            'file'                  => $image != "" ? $image : $ticket->file,
            'priority'              => $request->priority,
        ]);
    }

    /**
     * Delete ticket
     */
    public function deleteData(Ticket $ticket)
    {
        try {
            // Log the deletion
            Log::info('Ticket deleted', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id(),
                'ticket_data' => $ticket->toArray()
            ]);

            return $ticket->delete();
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver deleteData: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Assign ticket to agent
     */
    public function assignToAgent(Ticket $ticket, $agentId)
    {
        try {
            $originalAgentId = $ticket->agent_id;

            $ticket->update([
                'agent_id' => $agentId,
                'assigned_at' => $agentId ? now() : null,
                'status' => $agentId ? ($ticket->status === 'open' ? 'in_progress' : $ticket->status) : 'open'
            ]);

            Log::info('Ticket assigned to agent', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id(),
                'original_agent_id' => $originalAgentId,
                'new_agent_id' => $agentId
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver assignToAgent: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id(),
                'agent_id' => $agentId
            ]);
            throw $e;
        }
    }

    /**
     * Resolve ticket
     */
    public function resolveTicket(Ticket $ticket, $resolvedById = null)
    {
        try {
            $resolvedBy = $resolvedById ?? Auth::id();

            $ticket->update([
                'status' => 'resolved',
                'resolved_by' => $resolvedBy,
                'resolved_at' => now()
            ]);

            Log::info('Ticket resolved', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id(),
                'resolved_by' => $resolvedBy
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver resolveTicket: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id(),
                'resolved_by_id' => $resolvedById
            ]);
            throw $e;
        }
    }

    /**
     * Reopen ticket
     */
    public function reopenTicket(Ticket $ticket)
    {
        try {
            $ticket->update([
                'status' => 'open',
                'resolved_by' => null,
                'resolved_at' => null
            ]);

            Log::info('Ticket reopened', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id()
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver reopenTicket: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Change ticket label (for kanban)
     */
    public function changeLabel(Ticket $ticket, $labelId)
    {
        try {
            $originalLabelId = $ticket->label_id;

            $ticket->update([
                'label_id' => $labelId
            ]);

            Log::info('Ticket label changed', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id(),
                'original_label_id' => $originalLabelId,
                'new_label_id' => $labelId
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver changeLabel: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id(),
                'label_id' => $labelId
            ]);
            throw $e;
        }
    }

    /**
     * Change ticket priority
     */
    public function changePriority(Ticket $ticket, $priority)
    {
        try {
            $originalPriority = $ticket->priority;

            $ticket->update([
                'priority' => $priority,
                'ticket_level' => $priority // Keep ticket_level in sync with priority
            ]);

            Log::info('Ticket priority changed', [
                'ticket_id' => $ticket->ticket_id,
                'id' => $ticket->id,
                'user_id' => Auth::id(),
                'original_priority' => $originalPriority,
                'new_priority' => $priority
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver changePriority: ' . $e->getMessage(), [
                'ticket_id' => $ticket->ticket_id ?? null,
                'user_id' => Auth::id(),
                'priority' => $priority
            ]);
            throw $e;
        }
    }

    /**
     * Get ticket statistics
     */
    public function getStatistics(Request $request = null)
    {
        try {
            $query = Ticket::query();

            // Apply date filter if provided
            if ($request && $request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->get('start_date'));
            }

            if ($request && $request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->get('end_date'));
            }

            $stats = [
                'total_tickets' => $query->count(),
                'open_tickets' => $query->where('status', 'open')->count(),
                'in_progress_tickets' => $query->where('status', 'in_progress')->count(),
                'pending_tickets' => $query->where('status', 'pending')->count(),
                'resolved_tickets' => $query->where('status', 'resolved')->count(),
                'closed_tickets' => $query->where('status', 'closed')->count(),
                'assigned_tickets' => $query->whereNotNull('agent_id')->count(),
                'unassigned_tickets' => $query->whereNull('agent_id')->count(),
                'high_priority_tickets' => $query->whereIn('priority', ['high', 'urgent'])->count(),
                'overdue_tickets' => $query->where('created_at', '<', now()->subDays(3))
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->count()
            ];

            // Priority breakdown
            $priorityStats = Ticket::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();

            $stats['priority_breakdown'] = $priorityStats;

            // Status breakdown
            $statusStats = Ticket::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $stats['status_breakdown'] = $statusStats;

            Log::info('Ticket statistics retrieved', [
                'user_id' => Auth::id(),
                'stats' => $stats
            ]);

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in TicketObserver getStatistics: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

     public function deleting(Ticket $ticket)
    {
        TicketAgent::where('ticket_id', $ticket->id)->delete();
        TicketLog::where('ticket_id', $ticket->id)->delete();
        TicketNote::where('ticket_id', $ticket->id)->delete();

    }
}
