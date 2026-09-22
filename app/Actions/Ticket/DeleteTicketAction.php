<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;

class DeleteTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function execute(Ticket $ticket): void
    {
        $this->ticketRepository->delete($ticket);
    }
}
