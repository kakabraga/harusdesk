<?php

namespace App\DTOs\Ticket;

class ListTicketDTO
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?int $sectorId = null,
        public readonly ?int $enterpriseId = null,
        public readonly int $perPage = 15,
    ) {}
}
