<?php

namespace App\Actions\Attachments;

use App\Actions\File\DeleteFileAction;
use App\Models\Attachment;
use App\Repositories\Eloquent\Attachment\AttachmentRepositoryInterface;

class ForceDeleteAttachmentAction
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository,
        private DeleteFileAction $deleteFileAction
    ) {}

    public function execute(Attachment $attachment): bool
    {
        $this->deleteFileAction->execute($attachment->path);

        return $this->attachmentRepository->forceDelete($attachment);
    }
}
