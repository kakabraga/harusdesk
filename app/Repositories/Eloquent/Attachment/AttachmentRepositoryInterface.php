<?php

namespace App\Repositories\Eloquent\Attachment;

use App\DTOs\Attachment\CreateAttachmentDTO;
use App\DTOs\Attachment\ListAttachmentDTO;
use App\Models\Attachment;
use App\Models\user;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttachmentRepositoryInterface
{
    public function create(CreateAttachmentDTO $dto): Attachment;

    public function delete(Attachment $attachment): void;

    public function listAllByTicketId(int $ticketId): array;

    public function listAllByInteractionId(int $interactionId): array;

    public function listAll(ListAttachmentDTO $dto, user $user): LengthAwarePaginator;

    public function updateAttachmentPath(Attachment $attachment, string $newPath): bool;

    public function hasOtherAttachments(int $ticketId, int $excludeAttachmentId): bool;

    public function forceDelete(Attachment $attachment): bool;
}
