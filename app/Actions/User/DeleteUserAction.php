<?php

namespace App\Actions\User;

use App\Models\User;
use App\Repositories\Eloquent\User\UserRepositoryInterface;

class DeleteUserAction
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(User $user): void
    {
        $this->repository->delete($user);
    }
}
