<?php

namespace App\DTOs\Sector;

class CreateSectorDTO
{
    public function __construct(
        public readonly string $enterpriseId,
        public readonly string $name
    ) {
    }
}