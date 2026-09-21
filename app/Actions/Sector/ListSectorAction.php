<?php

namespace App\Actions\Sector;

use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;

class ListSectorAction
{
    public function __construct(
        private SectorRepositoryInterface $repository
    ) {}

    public function execute()
    {
        return $this->repository->listAll();
    }
}
