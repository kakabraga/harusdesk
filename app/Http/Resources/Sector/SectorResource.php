<?php

namespace App\Http\Resources\Sector;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'enterprise_id' => $this->enterprise_id,
            'name' => $this->name,
            'active' => $this->active,
            'accepts_tickets' => $this->accepts_tickets,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];

    }
}
