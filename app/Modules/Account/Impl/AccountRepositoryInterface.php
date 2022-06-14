<?php

namespace App\Modules\Account\Impl;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function getAccountFromUser(User $user):Collection;
    public function saveAccount(User $user,array $accountInfo):Account;
    public function updateAccount(int $id, array $accountInfo):Account;
    public function deleteAccount(int $id):bool;
    public function getAccountById(int $id):Account;
    public function addUserToAccount($accountId, $userId):bool;
    public function userHasSharedAccount(int $accountId, int $userId):bool;
}
