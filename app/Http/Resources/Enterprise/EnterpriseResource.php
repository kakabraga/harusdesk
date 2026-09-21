<?php

namespace App\Http\Resources\Enterprise;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'email' => $this->email,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
