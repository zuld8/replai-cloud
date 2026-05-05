<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ticket\Ticket;
use App\Models\Master\Label;
use Illuminate\Http\Request;

class PublicTicketController extends Controller
{
    /**
     * Get ticket details by ticket_id for public tracking
     */
    public function show($ticketId)
    {
        try {
            // Find ticket by ticket_id (not UUID)
            $ticket = Ticket::where('ticket_id', $ticketId)
                ->with([
                    'category', 'ticketLogs.label', 'ticketLogs.agent',
                    'agents' => function($query) {
                        $query->select('users.id', 'users.name', 'users.email');
                    }
                ])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Get ticket notes separately
            $ticketNotes = \App\Models\Models\Ticket\TicketNote::where('ticket_id', $ticket->id)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get all labels for ticket type, ordered by position
            $allLabels = Label::where('type', 'ticket')
                ->orderBy('position', 'asc')
                ->get();

            // Create a map of label_id => log data from ticket logs
            $logsByLabelId = $ticket->ticketLogs->groupBy('label_id')->map(function($logs) {
                // Get all agents and the latest timestamp for this label
                $latestLog = $logs->sortByDesc('created_at')->first();
                
                // Collect all unique agents
                $agents = $logs->map(function($log) {
                    return $log->agent ? [
                        'id' => $log->agent->id,
                        'name' => $log->agent->name
                    ] : null;
                })->filter()->unique('id')->values();
                
                return [
                    'id' => $latestLog->id,
                    'agents' => $agents, // Multiple agents
                    'log_time' => $latestLog->log_time,
                    'created_at' => $latestLog->created_at->format('Y-m-d H:i:s')
                ];
            });

            // Build the complete label process list
            $labelProcess = $allLabels->map(function($label) use ($logsByLabelId) {
                $log = $logsByLabelId->get($label->id);
                
                return [
                    'label' => [
                        'id' => $label->id,
                        'name' => $label->name,
                        'color' => $label->color,
                        'position' => $label->position
                    ],
                    'log' => $log ? [
                        'id' => $log['id'],
                        'agents' => $log['agents'],
                        'log_time' => $log['log_time'],
                        'created_at' => $log['created_at']
                    ] : null
                ];
            });

            // Return only public-safe information
            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'subject' => $ticket->title, // Using 'title' field from database
                    'description' => $ticket->notes, // Using 'notes' field from database
                    'status' => ucfirst(str_replace('_', ' ', $ticket->status)), // Format status
                    'ticket_level' => ucfirst($ticket->ticket_level),
                    'category' => $ticket->category ? [
                        'id' => $ticket->category->id,
                        'name' => $ticket->category->name,
                    ] : null,
                    'agents' => $ticket->agents ? $ticket->agents->map(function($agent) {
                        return [
                            'id' => $agent->id,
                            'name' => $agent->name,
                            'pivot' => [
                                'role' => $agent->pivot->role ?? 'assigned',
                            ]
                        ];
                    }) : [],
                    'notes' => $ticketNotes->map(function($note) {
                        return [
                            'id' => $note->id,
                            'note' => $note->note,
                            'user' => [
                                'name' => optional($note->user)->name ?? 'Support Team'
                            ],
                            'created_at' => $note->created_at->toISOString(),
                        ];
                    }),
                    'created_at' => $ticket->created_at->toISOString(),
                    'updated_at' => $ticket->updated_at->toISOString(),
                    'label_process' => $labelProcess,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching ticket details'
            ], 500);
        }
    }
}
