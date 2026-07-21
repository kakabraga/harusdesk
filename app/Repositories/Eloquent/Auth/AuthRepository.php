<?php

namespace App\Repositories\Eloquent\Auth;

use App\Repositories\Eloquent\Auth\AuthRepositoryInterface;
use App\Models\User;
use App\DTOs\Auth\LoginUserDTO;
class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::withoutGlobalScopes()->where('email', $email)->first();
    }
}