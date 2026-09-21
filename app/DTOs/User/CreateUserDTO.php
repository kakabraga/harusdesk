<?php

namespace App\DTOs\User;

class CreateUserDTO
{
    public function __construct(
        public readonly int $enterprise_id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $newPassword,
        public readonly string $role,
    ) {}
}
