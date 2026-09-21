<?php

namespace App\Actions\User;

use App\Repositories\Eloquent\User\UserRepositoryInterface;

class GetUserLoggedAciton
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(int $userId)
    {
        return $this->repository->getUserById($userId);
    }
}
