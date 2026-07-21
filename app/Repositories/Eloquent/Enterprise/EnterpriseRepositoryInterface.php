<?php

namespace App\Repositories\Eloquent\Enterprise;

use App\Models\Enterprise;
use App\DTOs\Enterprise\EnterpriseDTO;

interface EnterpriseRepositoryInterface
{

    public function create(EnterpriseDTO $data): Enterprise;

}