<?php

namespace App\Actions\Ticket;

use App\DTOs\Ticket\CreateTicketDTO;
use App\Models\Ticket;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;

class CreateTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function execute(CreateTicketDTO $dto): Ticket
    {
        return $this->ticketRepository->create($dto);
    }
}
