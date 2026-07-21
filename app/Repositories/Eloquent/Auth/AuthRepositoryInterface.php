<?php

namespace App\Repositories\Eloquent\Auth;

use App\DTOs\Auth\LoginUserDTO;
use App\Models\User;
interface AuthRepositoryInterface
{

    public function findByEmail(string $email): ?User;
}