<?php

namespace App\Repositories\Eloquent\Ticket;

use App\DTOs\Ticket\CreateTicketDTO;
use App\DTOs\Ticket\ListTicketDTO;
use App\DTOs\Ticket\UpdateTicketDTO;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TicketRepositoryInterface
{
    public function create(CreateTicketDTO $dto): Ticket;

    public function listAll(): Collection;

    public function list(ListTicketDTO $dto, User $user): LengthAwarePaginator;

    public function update(Ticket $ticket, UpdateTicketDTO $dto): Ticket;

    public function delete(Ticket $ticket): void;
}
