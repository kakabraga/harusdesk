<?php

namespace App\Actions\Attachments;

use App\Actions\File\MoveFileAction;
use App\Models\Attachment;
use App\Repositories\Eloquent\Attachment\AttachmentRepositoryInterface;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteAttachmentAction
{
    private const TRASH_PATH = 'trash/';

    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository,
        private TicketRepositoryInterface $ticketRepository,
        private MoveFileAction $moveFileAction
    ) {}

    public function execute(Attachment $attachment): bool
    {
        $newPath = $this->getNewPath($attachment->path);

        if (! $this->moveFileAction->execute($attachment->path, $newPath)) {
            return false;
        }

        return DB::transaction(function () use ($attachment, $newPath) {
            $this->updateAttachmentPath($attachment, $newPath);
            $this->attachmentRepository->delete($attachment);
            $this->updateTicketHasAttachments($attachment);

            return true;
        });
    }

    private function updateAttachmentPath(Attachment $attachment, string $newPath): bool
    {
        return $this->attachmentRepository->updateAttachmentPath($attachment, $newPath);
    }

    private function updateTicketHasAttachments(Attachment $attachment): void
    {
        $hasRemainingAttachments = $this->attachmentRepository->hasOtherAttachments(
            $attachment->ticket_id,
            $attachment->id
        );

        $this->ticketRepository->updateHasAttachments($attachment->ticket, $hasRemainingAttachments);
    }

    private function getNewPath(string $oldPath): string
    {
        return self::TRASH_PATH.$oldPath;
    }
}
