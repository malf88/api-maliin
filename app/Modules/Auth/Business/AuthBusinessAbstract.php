<?php

namespace App\Modules\Auth\Business;

use App\Models\User;
use App\Modules\Auth\Impl\AuthRepositoryInterface;

abstract class AuthBusinessAbstract
{
    public function __construct(
        protected readonly AuthRepositoryInterface $authRepository
    )
    {
    }

    public function logout(User $user):bool
    {
        return $this->authRepository->logoutUser($user);
    }
}
