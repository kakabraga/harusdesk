<?php

namespace App\Repositories\Eloquent\Ticket;

use App\DTOs\Ticket\CreateTicketDTO;
use App\DTOs\Ticket\ListTicketDTO;
use App\DTOs\Ticket\UpdateTicketDTO;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository implements TicketRepositoryInterface
{
    public function create(CreateTicketDTO $dto): Ticket
    {
        return Ticket::create([
            'enterprise_id' => $dto->enterpriseId,
            'sector_id' => $dto->sectorId,
            'requester_id' => $dto->requesterId,
            'title' => $dto->title,
            'description' => $dto->description,
            'priority' => $dto->priority,
            'status' => $dto->status,
            'attendant_id' => $dto->attendantId,
        ]);
    }

    public function listAll(): Collection
    {
        return Ticket::all();
    }

    public function list(ListTicketDTO $dto, User $user): LengthAwarePaginator
    {
        $query = Ticket::query();

        $this->applyUserScope($query, $user, $dto);
        $this->applyFilters($query, $dto);

        return $query->latest()->paginate($dto->perPage);
    }

    private function applyUserScope(Builder $query, User $user, ListTicketDTO $dto): void
    {
        if ($user->isSuperAdmin()) {
            $query->when($dto->enterpriseId, fn (Builder $q, int $enterpriseId) => $q->where('enterprise_id', $enterpriseId));

            return;
        }

        if ($user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);

            return;
        }

        $this->applyRegularUserScope($query, $user);
    }

    private function applyRegularUserScope(Builder $query, User $user): void
    {
        $userSectorIds = $user->sectors()->pluck('sectors.id')->toArray();

        $query->where('enterprise_id', $user->enterprise_id)
            ->where(function (Builder $q) use ($user, $userSectorIds) {
                $q->where('requester_id', $user->id)
                    ->orWhereIn('sector_id', $userSectorIds);
            });
    }

    private function applyFilters(Builder $query, ListTicketDTO $dto): void
    {
        $query->when($dto->status, fn (Builder $q, string $status) => $q->where('status', $status))
            ->when($dto->priority, fn (Builder $q, string $priority) => $q->where('priority', $priority))
            ->when($dto->sectorId, fn (Builder $q, int $sectorId) => $q->where('sector_id', $sectorId));
    }

    public function update(Ticket $ticket, UpdateTicketDTO $dto): Ticket
    {
        $ticket->update($dto->toArray());

        return $ticket;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
    }
}
