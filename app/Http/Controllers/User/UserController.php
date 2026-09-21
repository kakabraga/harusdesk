<?php

namespace App\Http\Controllers\User;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\User\CreateUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        return ApiResponse::success(new CreateUserResource($action->execute($request->toDto())));
    }

    public function destroy(User $user, DeleteUserAction $action)
    {
        $this->authorize('delete', $user);

        $action->execute($user);

        return response()->noContent();
    }

    public function me(Request $request)
    {
        return ApiResponse::success(new CreateUserResource($request->user()));
    }
}
