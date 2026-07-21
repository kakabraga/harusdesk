<?php

namespace App\Http\Controllers\Enterprise;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enterprise\RegisterEnterpriseRequest;
use App\Actions\Enterprise\RegisterEnterpriseAction;
use App\Http\Resources\Enterprise\RegisterEnterpriseResource;

class EnterpriseController extends Controller
{

    public function register(RegisterEnterpriseRequest $request, RegisterEnterpriseAction $action)
    {
        return ApiResponse::success(new RegisterEnterpriseResource($action->execute($request->toDTO())), 'Enterprise registered successfully.');

    }
}
