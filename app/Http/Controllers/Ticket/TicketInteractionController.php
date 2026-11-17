<?php

namespace App\Http\Controllers\Ticket;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Interaction\InteractionResource;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketInteractionController extends Controller
{
    /**
     * List all interactions for a specific ticket.
     */
    public function index(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $interactions = $ticket->interactions()
            ->with(['user:id,name,email,role', 'attachments'])
            ->oldest()
            ->get();

        return ApiResponse::success(InteractionResource::collection($interactions));
    }

    /**
     * Store a new interaction message for a ticket.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1'],
            'status' => ['nullable', 'string', 'in:open,in_progress,concluded,cancelled,reopened'],
        ]);

        $interaction = $ticket->interactions()->create([
            'enterprise_id' => $ticket->enterprise_id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'has_attachments' => false,
        ]);

        $updates = [];
        if (! empty($validated['status'])) {
            $updates['status'] = $validated['status'];
        }

        // If ticket has no attendant assigned and responder is not the requester, assign responder
        if (! $ticket->attendant_id && $request->user()->id !== $ticket->requester_id) {
            $updates['attendant_id'] = $request->user()->id;
        }

        if (! empty($updates)) {
            $ticket->update($updates);
        }

        $interaction->load(['user:id,name,email,role', 'attachments']);

        return ApiResponse::success(new InteractionResource($interaction), 201);
    }
}
