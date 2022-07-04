<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\Impl\Business\UserBusinessInterface;
use App\Modules\Account\Impl\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserBusiness implements UserBusinessInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    )
    {
    }

    public function generateUserByEmail($email): User
    {
        $user = new User([
            'email' => $email,
            'first_name'=> $email,
            'last_name' => $email,
            'password' => Hash::make(uniqid())
        ]);

        $user = $this->userRepository->saveUser($user);
    }

    public function findUserByEmail(string $email): User|null
    {
        return $this->userRepository->findUserByEmail($email);
    }

    public function findUserOrGenerateByEmail(string $email): User
    {
        if($this->userRepository->existsUserByEmail($email)){
            return $this->findUserByEmail($email);
        }else{
            return $this->generateUserByEmail($email);
        }
    }
}
