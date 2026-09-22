<?php

namespace App\Actions\Ticket;

use App\DTOs\Ticket\UpdateTicketDTO;
use App\Models\Ticket;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;

class UpdateTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function execute(Ticket $ticket, UpdateTicketDTO $dto): Ticket
    {
        return $this->ticketRepository->update($ticket, $dto);
    }
}
