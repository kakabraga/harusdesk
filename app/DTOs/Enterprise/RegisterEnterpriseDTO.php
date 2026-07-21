<?php

namespace App\DTOs\Enterprise;

use App\DTOs\User\UserDTO;

readonly class RegisterEnterpriseDTO
{
    public function __construct(
        public EnterpriseDTO $enterprise,
        public UserDTO $userAdmin,
    ) {
    }
}