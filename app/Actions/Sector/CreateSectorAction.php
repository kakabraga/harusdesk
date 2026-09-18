<?php

namespace App\Actions\Sector;

use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\DTOs\Sector\CreateSectorDTO;
use App\Models\Sector;
class CreateSectorAction
{

    public function __construct(
        private SectorRepositoryInterface $repository
    ) {
    }

    public function execute(CreateSectorDTO $dto): Sector
    {
        return $this->repository->create($dto);
    }
}