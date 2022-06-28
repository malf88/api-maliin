<?php

namespace App\Modules\Account\Repository;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\RelationNotFoundException;

class AccountRepository implements AccountRepositoryInterface
{
    public function getAccountFromUser(User $user):Collection
    {
        $listAccount = Account::select('accounts.*')
            ->leftJoin('maliin.accounts_users', 'accounts_users.account_id', '=', 'accounts.id')
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
    public function userHasSharedAccount(int $accountId, int $userId):bool
    {
        $account = Account::find($accountId);
        return $account->sharedUsers()->where('user_id', $userId)->exists();
    }

    public function addUserToAccount($accountId, $userId): bool
    {
        $account = Account::find($accountId);
        try{
            $account->sharedUsers()->attach($userId);
            return $account->save();
        }catch (RelationNotFoundException $e){
            return false;
        }

    }

    public function removeUserToAccount($accountId, $userId): bool
    {
        $account = Account::find($accountId);
        try{
            $account->sharedUsers()->detach($userId);
            return $account->save();
        }catch (RelationNotFoundException $e){
            return false;
        }
    }
}
