<?php

namespace App\DTOs\Attachment;

class CreateAttachmentDTO
{
    public function __construct(
        public readonly ?int $ticketId,
        public readonly ?int $interactionId,
        public readonly string $originalName,
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $sizeKb
    ) {}
}
