<?php

namespace App\Modules\Auth\Impl;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findUserByGoogleId(string $googleId):User;
    public function insertUser(User $user):User;
    public function logoutUser(User $user):bool;
}
