<?php

namespace App\Http\Resources\Sector;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'enterprise_id' => $this->enterprise_id,
            'name' => $this->name,
            'active' => $this->active
        ];

    }
}