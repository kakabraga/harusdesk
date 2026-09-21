<?php

namespace App\Http\Resources\Enterprise;

use App\Http\Resources\Auth\UserAdminResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterEnterpriseResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'enterprise' => new EnterpriseResource($this['enterprise']),
            'userAdmin' => new UserAdminResource($this['userAdmin']),
            'token' => $this['token'],
        ];
    }
}
