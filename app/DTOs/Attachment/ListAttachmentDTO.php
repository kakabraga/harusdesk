<?php

namespace App\DTOs\Attachment;

class ListAttachmentDTO
{
    public function __construct(
        public readonly ?int $enterpriseId = null,
        public readonly ?int $sectorId = null,
        public readonly ?int $ticketId = null,
        public readonly ?int $interactionId = null,
        public readonly ?string $originalName = null,
        public readonly ?string $mimeType = null,
        public readonly ?int $minSizeKb = null,
        public readonly ?int $maxSizeKb = null,
        public readonly ?int $perPage = 15,
    ) {}
}
