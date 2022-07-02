<?php

namespace App\Modules\Auth\Repository;

use App\Models\User;
use App\Modules\Auth\Impl\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{

    public function findUserByGoogleId(string $googleId): User
    {
        $user = User::where('google_id', $googleId)->first();

        return $user;
    }

    public function insertUser(User $user): User
    {
        $user->save();
        return $user;
    }

    public function logoutUser(User $user): bool
    {
        return $user->tokens()->where('token', $user->currentAccessToken()->token)->delete();
    }
}
