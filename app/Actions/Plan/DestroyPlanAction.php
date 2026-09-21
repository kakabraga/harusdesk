<?php

namespace App\Actions\Plan;

use App\Models\Plan;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;

class DestroyPlanAction
{
    public function __construct(
        private PlanRepositoryInterface $repository
    ) {}

    public function execute(Plan $plan): void
    {

        $this->repository->delete($plan);

    }
}
