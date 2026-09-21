<?php

namespace App\DTOs\Sector;

class CreateSectorDTO
{
    public function __construct(
        public readonly int $enterpriseId,
        public readonly string $name,
        public readonly ?bool $active = true,
        public readonly ?bool $acceptsTickets = false
    ) {}
}
