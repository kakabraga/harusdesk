<?php

namespace App\Actions\Sector;

use App\DTOs\Sector\UpdateSectorDTO;
use App\Models\Sector;
use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;

class UpdateSectorAction
{
    public function __construct(
        private SectorRepositoryInterface $repository
    ) {}

    public function execute(Sector $sector, UpdateSectorDTO $dto): Sector
    {
        return $this->repository->update($sector, $dto->toArray());
    }
}
