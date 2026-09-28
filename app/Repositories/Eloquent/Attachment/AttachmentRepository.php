<?php

namespace App\Repositories\Eloquent\Attachment;

use App\DTOs\Attachment\CreateAttachmentDTO;
use App\DTOs\Attachment\ListAttachmentDTO;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function create(CreateAttachmentDTO $dto): Attachment
    {
        return Attachment::create([
            'ticket_id' => $dto->ticketId,
            'interaction_id' => $dto->interactionId,
            'original_name' => $dto->originalName,
            'path' => $dto->path,
            'mime_type' => $dto->mimeType,
            'size_kb' => $dto->sizeKb,
        ]);
    }

    public function listAllByTicketId(int $ticketId): array
    {
        return Attachment::where('ticket_id', $ticketId)->get()->all();
    }

    public function listAllByInteractionId(int $interactionId): array
    {
        return Attachment::where('interaction_id', $interactionId)->get()->all();
    }

    public function listAll(ListAttachmentDTO $dto, User $user): LengthAwarePaginator
    {
        $query = Attachment::query();

        $this->applyFilters($query, $dto);

        $this->applyUserScope($query, $user, $dto);

        return $query->latest()->paginate($dto->perPage);
    }

    public function updateAttachmentPath(Attachment $attachment, string $newPath): bool
    {
        $attachment->path = $newPath;

        return $attachment->save();
    }

    /**
     * Apply the user scope to the query.
     */
    private function applyUserScope(Builder $query, User $user, ListAttachmentDTO $dto): void
    {
        if ($user->isSuperAdmin()) {
            $query->when($dto->enterpriseId, function (Builder $q, int $enterpriseId) {
                $q->whereHas('ticket', fn (Builder $ticketQuery) => $ticketQuery->where('enterprise_id', $enterpriseId));
            });

            return;
        }

        if ($user->isAdmin()) {
            $query->whereHas('ticket', function (Builder $ticketQuery) use ($user) {
                $ticketQuery->where('enterprise_id', $user->enterprise_id);
            });

            return;
        }

        $this->applyRegularUserScope($query, $user);
    }

    private function applyRegularUserScope(Builder $query, User $user): void
    {
        $userSectorIds = $user->sectors()->pluck('sectors.id')->toArray();

        $query->whereHas('ticket', function (Builder $ticketQuery) use ($user, $userSectorIds) {
            $ticketQuery->where('enterprise_id', $user->enterprise_id)
                ->where(function (Builder $sub) use ($user, $userSectorIds) {
                    $sub->where('requester_id', $user->id)
                        ->orWhereIn('sector_id', $userSectorIds);
                });
        });
    }

    private function applyFilters(Builder $query, ListAttachmentDTO $dto): void
    {
        $query
            ->when($dto->sectorId, function (Builder $q, int $sectorId) {
                $q->whereHas('ticket', fn (Builder $ticketQuery) => $ticketQuery->where('sector_id', $sectorId));
            })
            ->when($dto->ticketId, fn (Builder $q, int $ticketId) => $q->where('ticket_id', $ticketId))
            ->when($dto->interactionId, fn (Builder $q, int $interactionId) => $q->where('interaction_id', $interactionId))
            ->when($dto->originalName, fn (Builder $q, string $name) => $q->where('original_name', 'like', "%{$name}%"))
            ->when($dto->mimeType, fn (Builder $q, string $mimeType) => $q->where('mime_type', $mimeType))
            ->when($dto->minSizeKb, fn (Builder $q, int $min) => $q->where('size_kb', '>=', $min))
            ->when($dto->maxSizeKb, fn (Builder $q, int $max) => $q->where('size_kb', '<=', $max));
    }

    public function delete(Attachment $attachment): void
    {
        $attachment->delete();
    }

    public function forceDelete(Attachment $attachment): bool
    {
        return $attachment->forceDelete();
    }

    public function hasOtherAttachments(int $ticketId, int $excludeAttachmentId): bool
    {
        return Attachment::where('ticket_id', $ticketId)
            ->where('id', '!=', $excludeAttachmentId)
            ->exists();
    }
}
