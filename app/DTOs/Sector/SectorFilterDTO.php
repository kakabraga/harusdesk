<?php

namespace App\DTOs\Sector;

class SectorFilterDTO
{
    public function __construct(
        public ?string $name,
        public ?int $active,
        public int $perPage = 10
    ) {}
}
