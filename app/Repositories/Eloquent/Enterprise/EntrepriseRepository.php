<?php

namespace App\Repositories\Eloquent\Enterprise;

use App\Repositories\Eloquent\Enterprise\EnterpriseRepositoryInterface;
use App\DTOs\Enterprise\EnterpriseDTO;
use App\Models\Enterprise;
class EntrepriseRepository implements EnterpriseRepositoryInterface
{

    public function create(EnterpriseDTO $dto): Enterprise
    {

        return Enterprise::create([
            'name' => $dto->name,
            'cnpj' => $dto->cnpj,
            'email' => $dto->email,
            'plan_id' => 1,
        ]);

    }
}