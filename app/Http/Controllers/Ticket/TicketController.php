<?php

namespace App\Http\Controllers\Ticket;

use App\Actions\Ticket\CreateTicketAction;
use App\Actions\Ticket\DeleteTicketAction;
use App\Actions\Ticket\ListTicketAction;
use App\Actions\Ticket\UpdateTicketAction;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ListTicketRequest;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListTicketRequest $request, ListTicketAction $action)
    {
        $this->authorize('viewAny', Ticket::class);

        return ApiResponse::success(TicketResource::collection($action->execute($request->toDTO(), $request->user())));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request, CreateTicketAction $action)
    {
        return ApiResponse::success(new TicketResource($action->execute($request->toDTO())));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return ApiResponse::success(new TicketResource($ticket));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket, UpdateTicketAction $action)
    {
        $this->authorize('update', $ticket);

        return ApiResponse::success(new TicketResource($action->execute($ticket, $request->toDTO())));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket, DeleteTicketAction $action)
    {
        $this->authorize('delete', $ticket);

        $action->execute($ticket);

        return response()->noContent();
    }
}
