<?php

namespace App\Actions\Enterprise;

use App\DTOs\Enterprise\EnterpriseDTO;
use App\DTOs\Enterprise\RegisterEnterpriseDTO;
use App\DTOs\User\UserDTO;
use App\Models\Enterprise;
use App\Repositories\Eloquent\Enterprise\EnterpriseRepositoryInterface;
use App\Repositories\Eloquent\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegisterEnterpriseAction
{
    public function __construct(
        private EnterpriseRepositoryInterface $enterpriseRepository,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function execute(RegisterEnterpriseDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {

            $enterprise = $this->createEnterprise($dto->enterprise);

            $user = $this->createAdminUser(
                $dto->userAdmin,
                $enterprise
            );

            return [
                'enterprise' => $enterprise,
                'userAdmin' => $user['user'],
                'token' => $user['token'],
            ];
        });
    }

    private function createEnterprise(EnterpriseDTO $dto): Enterprise
    {
        return $this->enterpriseRepository->create($dto);
    }

    private function createAdminUser(UserDTO $dto, Enterprise $enterprise): array
    {
        $user = $this->userRepository->createAdmin($dto, $enterprise);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
