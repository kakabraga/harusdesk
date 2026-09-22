<?php

namespace App\Repositories\Eloquent\Sector;

use App\DTOs\Sector\CreateSectorDTO;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Collection;

interface SectorRepositoryInterface
{
    public function create(CreateSectorDTO $data): Sector;

    public function update(Sector $sector, array $data): Sector;

    public function delete(Sector $sector): void;

    public function listAll(): Collection;

    public function findByIdAndEnterpriseId(int $sectorId, int $enterpriseId): ?Sector;
}
