<?php

namespace App\Repositories\Eloquent\Sector;

use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\Models\Sector;
use App\DTOs\Sector\CreateSectorDTO;
class SectorRepository implements SectorRepositoryInterface
{


    public function create(CreateSectorDTO $data): Sector
    {
        return Sector::create([
            'enterprise_id' => $data->enterpriseId,
            'name' => $data->name
        ]);
    }
}