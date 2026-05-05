<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Models\Ticket\Ticket;
use App\Models\Store\Store;
use App\Models\Master\Label;
use App\Models\User;
use App\Models\Ticket\TicketLog;
use App\Observers\Ticket\TicketObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class TicketController extends Controller
{
    protected $ticketObserver;
    protected $uploadImageProcess;

    public function __construct(TicketObserver $ticketObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->ticketObserver       = $ticketObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }

    /**
     * Kanban view (for web routes)
     */
    public function index(Request $request)
    {
        return view('tickets.kanban', ['page'  => 'Ticket', 'breadcumb' => true]);
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            // Get labels with tickets grouped by label
            $labels = Label::where('type', 'ticket')
                ->orderBy('position', 'asc')
                ->get();

            $labelsData = [];
            $perPage = (int) $request->get('per_page', 20);

            foreach ($labels as $label) {

                $query      = $this->ticketObserver->getData($request)->where('label_id', $label->id);
                $totalCount = $query->count();

                // Get paginated tickets
                $tickets = $query->orderBy('created_at', 'desc')
                    ->limit($perPage)
                    ->get()
                    ->map(function ($ticket) use ($label) {
                        return [
                            'id' => $ticket->id,
                            'ticket_id' => $ticket->ticket_id,
                            'title' => $ticket->title,
                            'ticket_name'   => $ticket->ticket_name,
                            'contacts' => array(
                                'id'        => $ticket->contact->id ?? null,
                                'name'      => $ticket->contact->name ?? ''
                            ),
                            'name' => $ticket->contact->name ?? 'Unknown',
                            'email' => $ticket->contact->email ?? '',
                            'phone' => $ticket->contact->phone ?? '',
                            'status' => $ticket->status,
                            'ticket_level' => $ticket->ticket_level,
                            'priority' => $ticket->priority ?? $ticket->ticket_level,
                            'title' => $ticket->title,
                            'notes' => $ticket->notes,
                            'from_channel' => $ticket->contact->from_channel ?? 'unknown',
                            'category_id' => $ticket->category_id,
                            'handled_by' => $ticket->agent ? [
                                'id' => $ticket->agent->id,
                                'name' => $ticket->agent->name,
                                'assigned_at' => $ticket->assigned_at
                            ] : null,
                            'agents' => $ticket->agents->map(function ($agent) {
                                return [
                                    'id' => $agent->user->id ?? null,
                                    'name' => $agent->user->name ?? '',
                                    'email' => $agent->user->email ?? '',
                                    'pivot' => [
                                        'role' => $agent->role,
                                        'assigned_at' => $agent->assigned_at
                                    ]
                                ];
                            }),
                            'resolved_by' => $ticket->resolvedBy ? [
                                'id' => $ticket->resolvedBy->id,
                                'name' => $ticket->resolvedBy->name,
                                'resolved_at' => $ticket->resolved_at
                            ] : null,
                            'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                            'label' => $label->name,
                            'label_id' => $label->id,
                            'age_hours' => $ticket->getAgeInHours(),
                            'priority_color' => $ticket->getPriorityColor(),
                            'status_class' => $ticket->getStatusClass(),
                            'TicketLogs' => $ticket->ticketLogs->map(function ($log) {
                                return [
                                    'id' => $log->id,
                                    'agent' => $log->agent ? [
                                        'id' => $log->agent->id,
                                        'name' => $log->agent->name
                                    ] : null,
                                    'label' => $log->label ? [
                                        'id' => $log->label->id,
                                        'name' => $log->label->name,
                                        'color' => $log->label->color
                                    ] : null,
                                    'log_time' => $log->log_time,
                                    'created_at' => $log->created_at->format('Y-m-d H:i:s')
                                ];
                            })
                        ];
                    });


                $labelsData[] = [
                    'id' => $label->id,
                    'name' => $label->name,
                    'color' => $label->color,
                    'tag' => $label->tag,
                    'position' => $label->position,
                    'total_stores' => $totalCount,
                    'loaded_count' => $tickets->count(),
                    'has_more' => $totalCount > $perPage,
                    'stores' => $tickets
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $labelsData,
                'message' => 'Tickets data retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tickets data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $query = $this->ticketObserver->getData($request);

            // Pagination parameters
            $perPage    = $request->get('per_page', 20);
            $page       = $request->get('page', 1);

            // Get paginated results with relationships
            $tickets = $query->with(['contact', 'label', 'agent', 'category', 'resolvedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Transform data
            $transformedTickets = $tickets->getCollection()->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'title' => $ticket->title,
                    'ticket_name' => $ticket->ticket_name,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority ?? $ticket->ticket_level,
                    'ticket_level' => $ticket->ticket_level,
                    'notes' => $ticket->notes,
                    'file' => $ticket->file,
                    'contact' => $ticket->contact ? [
                        'id' => $ticket->contact->id,
                        'name' => $ticket->contact->name,
                        'email' => $ticket->contact->email,
                        'phone' => $ticket->contact->phone
                    ] : null,
                    'label' => $ticket->label ? [
                        'id' => $ticket->label->id,
                        'name' => $ticket->label->name,
                        'color' => $ticket->label->color
                    ] : null,
                    'category' => $ticket->category ? [
                        'id' => $ticket->category->id,
                        'name' => $ticket->category->name
                    ] : null,
                    'agent' => $ticket->agent ? [
                        'id' => $ticket->agent->id,
                        'name' => $ticket->agent->name
                    ] : null,
                    'resolved_by' => $ticket->resolvedBy ? [
                        'id' => $ticket->resolvedBy->id,
                        'name' => $ticket->resolvedBy->name
                    ] : null,
                    'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : null,
                    'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('Y-m-d H:i:s') : null,
                    'age_hours' => $ticket->getAgeInHours(),
                    'is_resolved' => $ticket->isResolved(),
                    'is_assigned' => $ticket->isAssigned()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedTickets,
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                    'has_more' => $tickets->hasMorePages()
                ],
                'message' => 'Tickets retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tickets',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function loadMore(Request $request): JsonResponse
    {
        try {
            $labelId = $request->get('label_id');
            $offset = (int) $request->get('offset', 0);
            $perPage = (int) $request->get('per_page', 20);

            if (!$labelId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Label ID is required'
                ], 400);
            }

            // Get label
            $label = Label::find($labelId);
            if (!$label) {
                return response()->json([
                    'success' => false,
                    'message' => 'Label not found'
                ], 404);
            }

            // Build query with filters
            $query = $this->ticketObserver->getData($request)->where('label_id', $labelId);

            // Get total count
            $totalCount = $query->count();

            // Get paginated tickets with offset
            $tickets = $query->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get()
                ->map(function ($ticket) use ($label) {
                    return [
                        'id' => $ticket->id,
                        'ticket_id' => $ticket->ticket_id,
                        'title' => $ticket->title,
                        'ticket_name' => $ticket->ticket_name,
                        'contacts' => [
                            'id' => $ticket->contact->id ?? null,
                            'name' => $ticket->contact->name ?? ''
                        ],
                        'name' => $ticket->contact->name ?? 'Unknown',
                        'email' => $ticket->contact->email ?? '',
                        'phone' => $ticket->contact->phone ?? '',
                        'status' => $ticket->status,
                        'ticket_level' => $ticket->ticket_level,
                        'priority' => $ticket->priority ?? $ticket->ticket_level,
                        'notes' => $ticket->notes,
                        'from_channel' => $ticket->contact->from_channel ?? 'unknown',
                        'category_id' => $ticket->category_id,
                        'category' => $ticket->category ? [
                            'id' => $ticket->category->id,
                            'name' => $ticket->category->name
                        ] : null,
                        'handled_by' => $ticket->agent ? [
                            'id' => $ticket->agent->id,
                            'name' => $ticket->agent->name,
                            'assigned_at' => $ticket->assigned_at
                        ] : null,
                        'agents' => $ticket->agents->map(function ($agent) {
                            return [
                                'id' => $agent->user->id ?? null,
                                'name' => $agent->user->name ?? '',
                                'email' => $agent->user->email ?? '',
                                'pivot' => [
                                    'role' => $agent->role,
                                    'assigned_at' => $agent->assigned_at
                                ]
                            ];
                        }),
                        'resolved_by' => $ticket->resolvedBy ? [
                            'id' => $ticket->resolvedBy->id,
                            'name' => $ticket->resolvedBy->name,
                            'resolved_at' => $ticket->resolved_at
                        ] : null,
                        'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                        'label' => $label->name,
                        'label_id' => $label->id,
                        'age_hours' => $ticket->getAgeInHours(),
                        'priority_color' => $ticket->getPriorityColor(),
                        'status_class' => $ticket->getStatusClass(),
                        'TicketLogs' => $ticket->ticketLogs->map(function ($log) {
                            return [
                                'id' => $log->id,
                                'agent' => $log->agent ? [
                                    'id' => $log->agent->id,
                                    'name' => $log->agent->name
                                ] : null,
                                'label' => $log->label ? [
                                    'id' => $log->label->id,
                                    'name' => $log->label->name,
                                    'color' => $log->label->color
                                ] : null,
                                'log_time' => $log->log_time,
                                'created_at' => $log->created_at->format('Y-m-d H:i:s')
                            ];
                        })
                    ];
                });

            $newOffset = $offset + $tickets->count();
            $hasMore = $newOffset < $totalCount;

            return response()->json([
                'success' => true,
                'stores' => $tickets, // ← Key 'stores' sesuai dengan frontend
                'has_more' => $hasMore,
                'total' => $totalCount,
                'loaded' => $newOffset,
                'message' => 'More tickets loaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load more tickets',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function store(CreateTicketRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try {


            $image  = '';

            if ($request->file && isset($request->file['data'])) {
                $image =  $this->uploadImageProcess->uploadFile($request->file['data'], Uuid::uuid4()->toString(), 'tickets');
            }

            // Create ticket using observer
            $ticket = $this->ticketObserver->createData($request, $image);

            foreach ($request->agents as $index => $value) {
                $agent = $ticket->agents()->create([
                    'agent_id'  => $value['id'],
                    'role' => $index === 0 ? 'primary' : 'assigned',
                    'assigned_at' => now(),
                ]);

                $ticket->ticketLogs()->create([
                    'ticket_id' => $ticket->id,
                    'agent_id'  => $value['id'],
                    'label_id'  => $ticket->label_id,
                    'log_time' => now(),
                ]);
            }

            $ticket->load(['contact', 'label', 'category', 'agent', 'agents']);

            $formattedTicket = [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'title' => $ticket->title,
                'ticket_name' => $ticket->ticket_name,
                'contacts' => [
                    'id' => $ticket->contact->id ?? null,
                    'name' => $ticket->contact->name ?? ''
                ],
                'name' => $ticket->contact->name ?? 'Unknown',
                'email' => $ticket->contact->email ?? '',
                'phone' => $ticket->contact->phone ?? '',
                'status' => $ticket->status,
                'ticket_level' => $ticket->ticket_level,
                'priority' => $ticket->priority ?? $ticket->ticket_level,
                'notes' => $ticket->notes,
                'from_channel' => $ticket->contact->from_channel ?? 'unknown',
                'category_id' => $ticket->category_id,
                'category' => $ticket->category ? [
                    'id' => $ticket->category->id,
                    'name' => $ticket->category->name
                ] : null,
                'handled_by' => $ticket->agent ? [
                    'id' => $ticket->agent->id,
                    'name' => $ticket->agent->name,
                    'assigned_at' => $ticket->assigned_at
                ] : null,
                'agents' => $ticket->agents->map(function ($agent) {
                    return [
                        'id' => $agent->user->id ?? null,
                        'name' => $agent->user->name ?? '',
                        'email' => $agent->user->email ?? '',
                        'pivot' => [
                            'role' => $agent->role,
                            'assigned_at' => $agent->assigned_at
                        ]
                    ];
                }),
                'resolved_by' => $ticket->resolvedBy ? [
                    'id' => $ticket->resolvedBy->id,
                    'name' => $ticket->resolvedBy->name,
                    'resolved_at' => $ticket->resolved_at
                ] : null,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                'label' => $ticket->label->name ?? '',
                'label_id' => $ticket->label_id,
                'age_hours' => $ticket->getAgeInHours(),
                'priority_color' => $ticket->getPriorityColor(),
                'status_class' => $ticket->getStatusClass(),
                'TicketLogs' => $ticket->ticketLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'agent' => $log->agent ? [
                            'id' => $log->agent->id,
                            'name' => $log->agent->name
                        ] : null,
                        'label' => $log->label ? [
                            'id' => $log->label->id,
                            'name' => $log->label->name,
                            'color' => $log->label->color
                        ] : null,
                        'log_time' => $log->log_time,
                        'created_at' => $log->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ];

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $formattedTicket,
                'message' => 'Ticket created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $ticket = Ticket::with(['contact', 'label', 'category', 'agent', 'resolvedBy'])
                ->find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            $ticketData = [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'title' => $ticket->title,
                'ticket_name' => $ticket->ticket_name,
                'status' => $ticket->status,
                'priority' => $ticket->priority ?? $ticket->ticket_level,
                'ticket_level' => $ticket->ticket_level,
                'notes' => $ticket->notes,
                'file' => $ticket->file,
                'file_url' => $ticket->file ? Storage::url('tickets/' . $ticket->file) : null,
                'contact' => $ticket->contact ? [
                    'id' => $ticket->contact->id,
                    'name' => $ticket->contact->name,
                    'email' => $ticket->contact->email,
                    'phone' => $ticket->contact->phone,
                    'address' => $ticket->contact->address ?? ''
                ] : null,
                'label' => $ticket->label ? [
                    'id' => $ticket->label->id,
                    'name' => $ticket->label->name,
                    'color' => $ticket->label->color
                ] : null,
                'category' => $ticket->category ? [
                    'id' => $ticket->category->id,
                    'name' => $ticket->category->name,
                    'description' => $ticket->category->description
                ] : null,
                'agent' => $ticket->agent ? [
                    'id' => $ticket->agent->id,
                    'name' => $ticket->agent->name,
                    'email' => $ticket->agent->email
                ] : null,
                'resolved_by' => $ticket->resolvedBy ? [
                    'id' => $ticket->resolvedBy->id,
                    'name' => $ticket->resolvedBy->name,
                    'email' => $ticket->resolvedBy->email
                ] : null,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : null,
                'assigned_at' => $ticket->assigned_at ? $ticket->assigned_at->format('Y-m-d H:i:s') : null,
                'age_hours' => $ticket->getAgeInHours(),
                'is_resolved' => $ticket->isResolved(),
                'is_assigned' => $ticket->isAssigned(),
                'priority_color' => $ticket->getPriorityColor(),
                'status_class' => $ticket->getStatusClass()
            ];

            return response()->json([
                'success' => true,
                'data' => $ticketData,
                'message' => 'Ticket details retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket details',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function edit(CreateTicketRequest $request, Ticket $ticket): JsonResponse
    {

        DB::beginTransaction();
        try {

            $image  = '';

            if ($request->file && isset($request->file['data'])) {
                $this->unlinkFile($ticket->file);
                $image =  $this->uploadImageProcess->uploadFile($request->file['data'], Uuid::uuid4()->toString(), 'tickets');
            }
            
            $oldLabelId = $ticket->label_id;

            $this->ticketObserver->updateData($request, $ticket, $image);

            if ($request->has('agents') && is_array($request->agents)) {

                $currentAgentIds = $ticket->agents()->pluck('agent_id')->toArray();

                $newAgentIds = [];
                foreach ($request->agents as $agent) {
                    $id = is_array($agent) ? ($agent['id'] ?? null) : $agent;
                    if ($id) $newAgentIds[] = $id;
                }

                $toAdd = array_diff($newAgentIds, $currentAgentIds);
                $toRemove = array_diff($currentAgentIds, $newAgentIds);

                if (!empty($toRemove)) {
                    $ticket->agents()->whereIn('agent_id', $toRemove)->delete();
                }

                foreach ($request->agents as $index => $agentData) {
                    $agentId = is_array($agentData) ? ($agentData['id'] ?? null) : $agentData;

                    if ($agentId && in_array($agentId, $toAdd)) {
                        $ticket->agents()->create([
                            'agent_id'  => $agentId,
                            'role' => $index === 0 ? 'primary' : 'assigned',
                            'assigned_at' => now(),
                        ]);

                        $ticket->ticketLogs()->create([
                            'ticket_id' => $ticket->id,
                            'agent_id'  => $agentId,
                            'label_id'  => $ticket->label_id,
                            'log_time' => now(),
                        ]);
                    }
                }
            }

            $formattedTicket = [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'title' => $ticket->title,
                'ticket_name' => $ticket->ticket_name,
                'contacts' => [
                    'id' => $ticket->contact->id ?? null,
                    'name' => $ticket->contact->name ?? ''
                ],
                'name' => $ticket->contact->name ?? 'Unknown',
                'email' => $ticket->contact->email ?? '',
                'phone' => $ticket->contact->phone ?? '',
                'status' => $ticket->status,
                'ticket_level' => $ticket->ticket_level,
                'priority' => $ticket->priority ?? $ticket->ticket_level,
                'notes' => $ticket->notes,
                'from_channel' => $ticket->contact->from_channel ?? 'unknown',
                'category_id' => $ticket->category_id,
                'category' => $ticket->category ? [
                    'id' => $ticket->category->id,
                    'name' => $ticket->category->name
                ] : null,
                'handled_by' => $ticket->agent ? [
                    'id' => $ticket->agent->id,
                    'name' => $ticket->agent->name,
                    'assigned_at' => $ticket->assigned_at
                ] : null,
                'agents' => $ticket->agents->map(function ($agent) {
                    return [
                        'id' => $agent->user->id ?? null,
                        'name' => $agent->user->name ?? '',
                        'email' => $agent->user->email ?? '',
                        'pivot' => [
                            'role' => $agent->role,
                            'assigned_at' => $agent->assigned_at
                        ]
                    ];
                }),
                'resolved_by' => $ticket->resolvedBy ? [
                    'id' => $ticket->resolvedBy->id,
                    'name' => $ticket->resolvedBy->name,
                    'resolved_at' => $ticket->resolved_at
                ] : null,
                'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                'label' => $ticket->label->name ?? '',
                'label_id' => $ticket->label_id,
                'age_hours' => $ticket->getAgeInHours(),
                'priority_color' => $ticket->getPriorityColor(),
                'status_class' => $ticket->getStatusClass(),
                'TicketLogs' => $ticket->ticketLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'agent' => $log->agent ? [
                            'id' => $log->agent->id,
                            'name' => $log->agent->name
                        ] : null,
                        'label' => $log->label ? [
                            'id' => $log->label->id,
                            'name' => $log->label->name,
                            'color' => $log->label->color
                        ] : null,
                        'log_time' => $log->log_time,
                        'created_at' => $log->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ];

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $formattedTicket,
                'old_label_id' => $oldLabelId,
                'label_changed' => $oldLabelId !== $ticket->label_id,
                'message' => 'Ticket updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Delete associated file if exists
            if ($ticket->file) {
                Storage::disk('public')->delete('tickets/' . $ticket->file);
            }

            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array|min:1',
                'ids.*' => 'uuid|exists:tickets,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ids = $request->get('ids');

            $ticketsWithFiles = Ticket::whereIn('id', $ids)
                ->whereNotNull('file')
                ->pluck('file');

            foreach ($ticketsWithFiles as $filename) {
                Storage::disk('public')->delete('tickets/' . $filename);
            }

            $deletedCount = Ticket::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'deleted_count' => $deletedCount,
                'message' => "{$deletedCount} tickets deleted successfully"
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tickets',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $searchQuery = $request->get('search', '');
            $perPage = $request->get('per_page', 15);

            if (empty($searchQuery)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Please provide search query'
                ]);
            }

            $tickets = Ticket::with(['contact', 'label', 'category', 'agent'])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('ticket_id', 'like', '%' . $searchQuery . '%')
                        ->orWhere('title', 'like', '%' . $searchQuery . '%')
                        ->orWhere('ticket_name', 'like', '%' . $searchQuery . '%')
                        ->orWhere('notes', 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('contact', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', '%' . $searchQuery . '%')
                                ->orWhere('email', 'like', '%' . $searchQuery . '%')
                                ->orWhere('phone', 'like', '%' . $searchQuery . '%');
                        });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tickets->items(),
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'last_page' => $tickets->lastPage()
                ],
                'message' => 'Search results retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to search tickets',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getAgents(Request $request): JsonResponse
    {
        try {
            $agents = User::select('id', 'name', 'email')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'agents' => $agents,
                'data' => $agents,
                'message' => 'Agents retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve agents',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getContacts(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');

            $query = Store::select('id', 'name', 'email', 'phone');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }

            $contacts = $query->orderBy('name', 'asc')
                ->limit(50)
                ->get();

            return response()->json([
                'success'   => true,
                'contacts'  => $contacts,
                'data'      => $contacts,
                'message'   => 'Contacts retrieved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|uuid|exists:tickets,id',
                'label_id' => 'required|uuid|exists:labels,id',
                'status' => 'nullable|in:open,in_progress,pending,resolved,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket = Ticket::find($request->get('ticket_id'));

            $updateData = [
                'label_id' => $request->get('label_id')
            ];

            if ($request->has('status')) {
                $updateData['status'] = $request->get('status');

                if ($request->get('status') === 'resolved' && $ticket->status !== 'resolved') {
                    $updateData['resolved_by'] = Auth::id();
                    $updateData['resolved_at'] = now();
                }
            }

            $ticket->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $ticket,
                'message' => 'Ticket status updated successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket status',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function moveTicket(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|uuid|exists:tickets,id',
                'label_id' => 'required|uuid|exists:labels,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticketId = $request->get('ticket_id');
            $labelId = $request->get('label_id');

            $ticket = Ticket::find($ticketId);
            $ticket->update(['label_id' => $labelId]);

            $ticket->load(['contact', 'label', 'category', 'agent']);

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'agent_id' => Auth::id(),
                'label_id' => $labelId,
                'log_time' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $ticket,
                'message' => 'Ticket moved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to move ticket',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function checkStorage($setting, $fileSize = 0)
    {
        if ($setting->merchant) {
            $totalSize  = 0;
            $path       = "uploads/folders/{$setting->id}";

            if (Storage::disk('local')->exists($path)) {
                $files = Storage::disk('local')->allFiles($path);
                foreach ($files as $file) {
                    $totalSize += Storage::disk('local')->size($file);
                }
            }

            // Convert to MB
            $usedStorageMB  = round($totalSize / 1024 / 1024, 2);
            $fileSizeMB     = round($fileSize / 1024 / 1024, 2);

            // Get total storage
            $storageFromSubscribe   = $setting->package_active ? (int)$setting->package_active->storage : 0;
            $storageFromAddons      = $setting->package_active_storage ? (int)$setting->package_active_storage->storage : 0;
            $totalStorage           = $storageFromSubscribe + $storageFromAddons;

            // Check if storage is available
            $remainingStorage = $totalStorage - $usedStorageMB;

            return [
                'available'         => $totalStorage > 0 && ($usedStorageMB + $fileSizeMB) <= $totalStorage,
                'total_storage'     => $totalStorage,
                'used_storage'      => $usedStorageMB,
                'remaining_storage' => $remainingStorage,
                'file_size'         => $fileSizeMB,
                'has_package'       => $totalStorage > 0
            ];
        } else {
            return [
                'available'         => true,
                'total_storage'     => 9999999,
                'used_storage'      => 0,
                'remaining_storage' => 9999,
                'file_size'         => 9999,
                'has_package'       => 9999
            ];
        }
    }
}
