<?php

namespace App\Http\Resources\Sector;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateSectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'enterprise_id' => $this->enterprise_id,
            'active' => $this->active
        ];
    }
}