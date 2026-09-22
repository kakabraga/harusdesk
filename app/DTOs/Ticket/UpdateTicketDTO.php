<?php

namespace App\DTOs\Ticket;

class UpdateTicketDTO
{
    public function __construct(
        public readonly ?int $sectorId = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $priority = null,
        public readonly ?string $status = null,
        public readonly ?int $attendantId = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'sector_id' => $this->sectorId,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'attendant_id' => $this->attendantId,
        ], fn ($value) => ! is_null($value));
    }
}
