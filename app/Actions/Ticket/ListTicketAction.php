<?php

namespace App\Actions\Ticket;

use App\DTOs\Ticket\ListTicketDTO;
use App\Models\User;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListTicketAction
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function execute(ListTicketDTO $dto, User $user): LengthAwarePaginator
    {
        return $this->ticketRepository->list($dto, $user);
    }
}
