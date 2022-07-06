<?php

namespace App\Modules\Account\Repository;

use App\Models\User;
use App\Modules\Account\Impl\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function existsUserByEmail(string $email): bool
    {
        return User::where('email',$email)->get()->count() > 0;
    }
    public function saveUser(array $userData):User
    {
        $user = new User($userData);
        $user->save();
        return $user;
    }

    public function findUserByEmail(string $email): User|null
    {
        return User::where('email',$email)->first();
    }

    public function updateUser(int $idUser, array $userData): User
    {
        $user = User::find($idUser);
        $user->fill($userData);
        $user->update();
        return $user;
    }
    public function getUserById(int $id):User
    {
        return User::find($id);
    }
}
