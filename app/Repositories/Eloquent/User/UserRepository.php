<?php

namespace App\Repositories\Eloquent\User;

use App\Repositories\Eloquent\User\UserRepositoryInterface;
use App\Models\User;
use App\Models\Enterprise;
use App\DTOs\User\UserDTO;
use App\DTOs\User\CreateUserDTO;
use RuntimeException;
class UserRepository implements UserRepositoryInterface
{


    public function createAdmin(UserDTO $dto, Enterprise $enterpise): User
    {
        return User::create([
            'enterprise_id' => $enterpise->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'role' => 'admin',
        ]);
    }
    public function create(CreateUserDTO $dto): User
    {
        return User::create([
            'enterprise_id' => $dto->enterprise_id,
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'role' => $dto->role,
        ]);
    }

    public function getUserById(int $userId): User
    {

        return User::where('id', $userId)->first();

    }

    public function delete(User $user): void
    {
        \Log::info('Antes do delete', [
            'id' => $user->id,
            'exists' => $user->exists,
        ]);

        $resultado = $user->delete();

        \Log::info('Depois do delete', [
            'resultado' => $resultado,
            'exists' => $user->exists,
        ]);
    }
}