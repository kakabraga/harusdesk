<?php

namespace App\Actions\Sector;

use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\Models\Sector;
class DeleteSectorAction
{


    public function __construct(
        private SectorRepositoryInterface $repository
    ) {
    }

    public function execute(Sector $sector): void
    {
        $this->repository->delete($sector);
    }
}