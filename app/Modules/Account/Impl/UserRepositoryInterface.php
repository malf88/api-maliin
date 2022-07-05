<?php

namespace App\Modules\Account\Impl;

use App\Models\User;

interface UserRepositoryInterface
{
    public function existsUserByEmail(string $email):bool;
    public function saveUser(array $user):User;
    public function findUserByEmail(string $email):User|null;
    public function updateUser(int $id, array $user):User;
    public function getUserById(int $id):User;
}
