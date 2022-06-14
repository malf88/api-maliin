<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AccountBusinessInterface
{
    public function getAccountById(int $id):Model;
    public function getListAllAccounts(User $user):Collection;
    public function getListAllAccountFromLoggedUser():Collection;
    public function insertAccount(User $user,array $accountInfo):Model;
    public function updateAccount(int $id,array $accountInfo):Model;
    public function deleteAccount(int $id):bool;
    public function addUserToAccount(int $accountId,int $userId):bool;
}
