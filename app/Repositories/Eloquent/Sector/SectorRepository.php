<?php

namespace App\Repositories\Eloquent\Sector;

use App\DTOs\Sector\CreateSectorDTO;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Collection;

class SectorRepository implements SectorRepositoryInterface
{
    public function create(CreateSectorDTO $data): Sector
    {
        return Sector::create([
            'enterprise_id' => $data->enterpriseId,
            'name' => $data->name,
            'active' => $data->active ?? true,
            'accepts_tickets' => $data->acceptsTickets ?? false,
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

    public function findByIdAndEnterpriseId(int $sectorId, int $enterpriseId): ?Sector
    {
        return Sector::withoutGlobalScopes()
            ->where('id', $sectorId)
            ->where('enterprise_id', $enterpriseId)
            ->first();
    }
}
