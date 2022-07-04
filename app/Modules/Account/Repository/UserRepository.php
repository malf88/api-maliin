<?php

namespace App\Modules\Account\Repository;

use App\Models\User;
use App\Modules\Account\Impl\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function existsUserByEmail(string $email): bool
    {

    }
    public function saveUser(User $user):User
    {

    }

    public function findUserByEmail(string $email): User|null
    {
        // TODO: Implement findUserByEmail() method.
    }
}
