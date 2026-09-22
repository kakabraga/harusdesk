<?php

namespace App\DTOs\Ticket;

class CreateTicketDTO
{
    public function __construct(
        public readonly int $enterpriseId,
        public readonly int $sectorId,
        public readonly int $requesterId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $priority = 'low',
        public readonly string $status = 'open',
        public readonly ?int $attendantId = null
    ) {}
}
