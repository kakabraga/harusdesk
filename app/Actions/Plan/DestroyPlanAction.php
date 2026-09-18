<?php

namespace App\Actions\Plan;

use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
use App\Models\Plan;
class DestroyPlanAction
{

    public function __construct(
        private PlanRepositoryInterface $repository
    ) {
    }

    public function execute(Plan $plan): void
    {

        $this->repository->delete($plan);

    }
}