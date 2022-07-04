<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;

interface UserBusinessInterface
{
    public function generateUserByEmail($email): User;
    public function findUserByEmail(string $email): User|null;
    public function findUserOrGenerateByEmail(string $email): User;
}
