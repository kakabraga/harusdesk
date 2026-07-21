<?php

namespace App\DTOs\Plan;

class PlanCreateDTO
{

    public function __construct(
        public readonly string $name,
        public readonly int $maxUsers,
        public readonly int $maxTicketsPerMonth,
        public readonly int $storageMb,
        public readonly float $price,
        public readonly bool $active = true,
    ) {
    }


}