<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Repositories\Eloquent\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use App\Models\User;
class LoginUserAction
{

    public function __construct(
        private AuthRepositoryInterface $repository,
    ) {
    }

    public function execute(LoginDTO $data): array
    {
        $user = $this->repository->findByEmail($data->email);

        $this->validateUserAndPassword($data, $user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];

    }

    private function validateUserAndPassword(LoginDTO $data, ?User $user): void
    {

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

    }
}