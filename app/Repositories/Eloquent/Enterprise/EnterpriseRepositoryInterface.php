<?php

namespace App\Repositories\Eloquent\Enterprise;

use App\DTOs\Enterprise\EnterpriseDTO;
use App\Models\Enterprise;

interface EnterpriseRepositoryInterface
{
    public function create(EnterpriseDTO $data): Enterprise;
}
