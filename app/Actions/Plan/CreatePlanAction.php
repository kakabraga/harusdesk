<?php

namespace App\Actions\Plan;

use App\DTOs\Plan\PlanCreateDTO;
use App\Repositories\Eloquent\Plan\PlanRepositoryInterface;
class CreatePlanAction
{

    public function __construct(
        private PlanRepositoryInterface $repository
    ) {
    }

    public function execute(PlanCreateDTO $dto)
    {

        return $this->repository->create($dto);
    }
}