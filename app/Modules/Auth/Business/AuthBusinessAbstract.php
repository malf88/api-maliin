<?php

namespace App\Modules\Auth\Business;

use App\Models\User;
use App\Modules\Account\Business\UserBusiness;
use App\Modules\Account\Impl\Business\UserBusinessInterface;
use App\Modules\Account\Impl\UserRepositoryInterface;
use App\Modules\Auth\Impl\AuthRepositoryInterface;
use Illuminate\Support\Facades\App;

abstract class AuthBusinessAbstract
    extends UserBusiness
{
    public function __construct(
        protected readonly AuthRepositoryInterface $authRepository,
        protected readonly UserRepositoryInterface $userRepository
    )
    {
        parent::__construct($this->userRepository);
    }

    public function logout(User $user):bool
    {
        return $this->authRepository->logoutUser($user);
    }
}
