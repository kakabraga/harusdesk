<?php

namespace App\Repositories\Eloquent\Sector;

use App\Repositories\Eloquent\Sector\SectorRepositoryInterface;
use App\Models\Sector;
use App\DTOs\Sector\CreateSectorDTO;
use App\DTOs\Sector\UpdateSectorDTO;
use Illuminate\Database\Eloquent\Collection;
class SectorRepository implements SectorRepositoryInterface
{


    public function create(CreateSectorDTO $data): Sector
    {
        return Sector::create([
            'enterprise_id' => $data->enterpriseId,
            'name' => $data->name
        ]);
    }

    public function delete(Sector $sector): void
    {
        $sector->delete();
    }

    public function update(Sector $sector, array $data): Sector
    {
        $sector->update($data);
        return $sector;
    }
    public function listAll(): Collection
    {
        return Sector::all();
    }


}