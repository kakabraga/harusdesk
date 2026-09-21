<?php

namespace App\Repositories\Eloquent\Plan;

use App\DTOs\Plan\PlanCreateDTO;
use App\Models\Plan;

interface PlanRepositoryInterface
{
    public function create(PlanCreateDTO $dto): Plan;

    public function delete(Plan $plan): void;
}
