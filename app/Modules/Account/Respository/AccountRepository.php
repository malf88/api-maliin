<?php

namespace App\Modules\Account\Respository;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AccountRepository
{
    public function getAccountFromUser(User $user):Collection{
        $listAccount = $user->accounts;
        return $listAccount;
    }
    public function saveAccount(User $user,array $accountInfo):Account{
        $account = new Account();
        $account->fill($accountInfo);

        $account->user_id = $user->id;
        $account->save();
        return $account;
    }
    public function updateAccount(int $id, array $accountInfo):Account{
        $account = Account::find($id);
        $account->fill($accountInfo);
        $account->update();
        return $account;
    }
    public function deleteAccount(int $id):bool{
        $account = Account::find($id);
        return $account->delete();
    }

    public function getAccountById(int $id):Account{
        return Account::find($id);
    }
}
