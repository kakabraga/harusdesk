<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\LoginUserAction;
use App\Helpers\ApiResponse;
use App\Http\Resources\Auth\LoginResource;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginUserAction $action)
    {

        return ApiResponse::success(new LoginResource($action->execute($request->toDTO())), 'User logged successfully');

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success([], 'Logout successfully');
    }
}
