<?php

namespace App\DTOs\User;

class RegisterUserDTO
{
    public function __construct(
        public readonly int $enterpise_id,
        public readonly string $adminName,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role,
        public readonly string $remember_token,
    ) {

    }
}