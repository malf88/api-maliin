<?php

namespace App\Modules\Account\Repository;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    public function getAccountFromUser(User $user):Collection
    {
        $listAccount = $user->accounts;
        return $listAccount;
    }
    public function saveAccount(User $user,array $accountInfo):Account
    {
        $account = new Account();
        $account->fill($accountInfo);

        $account->user_id = $user->id;
        $account->save();
        return $account;
    }
    public function updateAccount(int $id, array $accountInfo):Account
    {
        $account = Account::find($id);
        $account->fill($accountInfo);
        $account->update();
        return $account;
    }
    public function deleteAccount(int $id):bool
    {
        $account = Account::find($id);
        return $account->delete();
    }

    public function getAccountById(int $id):Account
    {
        return Account::find($id);
    }
}
