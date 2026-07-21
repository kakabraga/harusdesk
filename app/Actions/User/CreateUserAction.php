<?php

namespace App\Actions\User;

use App\DTOs\User\CreateUserDTO;
use App\Models\User;
use App\Repositories\Eloquent\User\UserRepositoryInterface;
class CreateUserAction
{

    public function __construct(
        private UserRepositoryInterface $repository
    ) {
    }

    public function execute(CreateUserDTO $dto): User
    {
        return $this->repository->create($dto);
    }
}