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
        $listAccount = Account::select('accounts.*')
            ->join('maliin.accounts_users', 'accounts_users.account_id', '=', 'accounts.id')
            ->where('accounts_users.user_id', $user->id)
            ->orWhere('accounts.user_id', $user->id)
            ->orderBy('name','ASC')
            ->with('user')
            ->get();
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
