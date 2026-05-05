<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket\Ticket;
use App\Models\Models\Ticket\TicketNote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TicketNoteController extends Controller
{
    /**
     * Add note to ticket
     * POST /app/tickets/{id}/notes
     */
    public function store(Request $request, $id): JsonResponse
    {
        try {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'note' => 'required|string|max:5000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $note = TicketNote::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'note' => $request->note
            ]);

            $note->load('user');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'user' => [
                        'id' => $note->user->id,
                        'name' => $note->user->name,
                        'email' => $note->user->email
                    ],
                    'created_at' => $note->created_at->format('Y-m-d H:i:s')
                ],
                'message' => 'Note added successfully'
            ], 201);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add note',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ticket notes
     * GET /app/tickets/{id}/notes
     */
    public function index(Request $request, $id): JsonResponse
    {
        try {
            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            $notes = $ticket->notes()->with('user')->get()->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'user' => [
                        'id' => $note->user->id,
                        'name' => $note->user->name,
                        'email' => $note->user->email
                    ],
                    'created_at' => $note->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notes
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notes',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
