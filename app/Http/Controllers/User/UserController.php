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
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('sectors');
        if (! $request->user()->isSuperAdmin()) {
            $query->where('enterprise_id', $request->user()->enterprise_id);
        }

        return ApiResponse::success($query->latest()->get(), 'Users retrieved successfully.');
    }

    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $this->authorize('create', User::class);

        $user = $action->execute($request->toDto());

        if ($request->has('sector_ids') && is_array($request->sector_ids)) {
            $user->sectors()->sync($request->sector_ids);
        }

        $user->load('sectors');

        return ApiResponse::success(new CreateUserResource($user), 'User created successfully.');
    }

    public function syncSectors(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'sector_ids' => ['nullable', 'array'],
            'sector_ids.*' => ['integer', 'exists:sectors,id'],
        ]);

        $user->sectors()->sync($request->input('sector_ids', []));
        $user->load('sectors');

        return ApiResponse::success(new CreateUserResource($user), 'Sectors synchronized successfully.');
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
