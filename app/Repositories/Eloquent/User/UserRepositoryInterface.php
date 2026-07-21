<?php

namespace App\Repositories\Eloquent\User;

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UserDTO;
use App\Models\User;
use App\Models\Enterprise;
interface UserRepositoryInterface
{

    public function createAdmin(UserDTO $user, Enterprise $enterprse): User;

    public function create(CreateUserDTO $user): User;

    public function getUserById(int $userId): User;

    public function delete(User $user): void;

}