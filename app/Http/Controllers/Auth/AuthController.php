<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\LoginUserAction;
use App\Helpers\ApiResponse;
use App\Http\Resources\Auth\LoginResource;
class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginUserAction $action)
    {

        return ApiResponse::success(new LoginResource($action->execute($request->toDTO())), 'User logged successfully');

    }
}
