<?php

namespace App\Actions\Attachments;

use App\DTOs\Attachment\ListAttachmentDTO;
use App\Models\User;
use App\Repositories\Eloquent\Attachment\AttachmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAttachmentAction
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository
    ) {}

    public function execute(ListAttachmentDTO $dto, User $user): LengthAwarePaginator
    {
        return $this->attachmentRepository->listAll($dto, $user);
    }
}
