<?php

namespace App\Modules\Auth\Impl;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findUserByGoogleId(string $googleId):User|null;
    public function insertUser(User $user):User;
    public function logoutUser(User $user):bool;
}
