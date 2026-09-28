<?php

namespace App\Actions\Attachments;

use App\Actions\File\UploadFileAction;
use App\DTOs\Attachment\CreateAttachmentDTO;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Repositories\Eloquent\Attachment\AttachmentRepositoryInterface;
use App\Repositories\Eloquent\Ticket\TicketRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AttachFilesToTicketAction
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository,
        private TicketRepositoryInterface $ticketRepository,
        private UploadFileAction $uploadFileAction
    ) {}

    /**
     * @param  UploadedFile[]  $files
     * @return Attachment[]
     */
    public function execute(array $files, Ticket $ticket): array
    {
        if (empty($files)) {
            return [];
        }

        $directory = "attachments/enterprise_{$ticket->enterprise_id}/ticket_{$ticket->id}";

        $dtos = $this->buildAttachmentDTOs($files, $directory, $ticket->id);

        return $this->storeAttachments($dtos, $ticket);
    }

    /**
     * @param  UploadedFile[]  $files
     * @return CreateAttachmentDTO[]
     */
    private function buildAttachmentDTOs(array $files, string $directory, int $ticketId): array
    {
        $dtos = [];

        foreach ($files as $file) {
            $uploadedFile = $this->uploadFileAction->execute($file, $directory);

            $dtos[] = new CreateAttachmentDTO(
                ticketId: $ticketId,
                interactionId: null,
                originalName: $uploadedFile->originalName,
                path: $uploadedFile->path,
                mimeType: $uploadedFile->mimeType,
                sizeKb: $uploadedFile->sizeKb
            );
        }

        return $dtos;
    }

    /**
     * @param  CreateAttachmentDTO[]  $dtos
     * @return Attachment[]
     */
    private function storeAttachments(array $dtos, Ticket $ticket): array
    {
        return DB::transaction(function () use ($dtos, $ticket) {
            $attachments = [];

            foreach ($dtos as $dto) {
                $attachments[] = $this->attachmentRepository->create($dto);
            }

            $this->ticketRepository->updateHasAttachments($ticket, true);

            return $attachments;
        });
    }
}
