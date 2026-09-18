<?php

namespace App\Repositories\Eloquent\Plan;

use App\DTOs\Plan\PlanCreateDTO;
use App\Models\Plan;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
class PlanRepository implements PlanRepositoryInterface
{

    public function create(PlanCreateDTO $dto): Plan
    {

        return Plan::create([
            'name' => $dto->name,
            'max_users' => $dto->maxUsers,
            'max_tickets_per_month' => $dto->maxTicketsPerMonth,
            'storage_mb' => $dto->storageMb,
            'price' => $dto->price,
            'active' => $dto->active,
        ]);
    }

    public function delete(Plan $plan): void
    {

        $plan->delete();

    }
}