<?php

namespace App\Repositories\Eloquent\Sector;

use App\Models\Sector;
use App\DTOs\Sector\CreateSectorDTO;
interface SectorRepositoryInterface
{

    public function create(CreateSectorDTO $data): Sector;
}