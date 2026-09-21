<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
