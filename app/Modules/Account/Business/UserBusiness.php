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
        $user = [
            'email' => $email,
            'first_name'=> '',
            'last_name' => '',
            'password' => Hash::make(uniqid())
        ];

        return $this->userRepository->saveUser($user);
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
    public function getUserById(int $id):User
    {
        return $this->userRepository->getUserById($id);
    }
}
