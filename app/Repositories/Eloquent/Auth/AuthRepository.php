<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::withoutGlobalScopes()->where('email', $email)->first();
    }
}
