<?php

namespace App\Http\Resources\Plan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatePlanResource extends JsonResource
{


    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'max_users' => $this->max_users,
            'max_tickets_per_month' => $this->max_tickets_per_month,
            'storage_mb' => $this->storage_mb,
            'price' => $this->price,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
