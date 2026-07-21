<?php

namespace App\DTOs\Enterprise;

readonly class EnterpriseDTO
{
    public function __construct(
        public string $name,
        public string $cnpj,
        public string $email,
        public int $planId,
    ) {
    }
}