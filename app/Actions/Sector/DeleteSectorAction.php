<?php

namespace App\Actions\Sector;

use App\Models\Sector;
use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;

class DeleteSectorAction
{
    public function __construct(
        private SectorRepositoryInterface $repository
    ) {}

    public function execute(Sector $sector): void
    {
        $this->repository->delete($sector);
    }
}
