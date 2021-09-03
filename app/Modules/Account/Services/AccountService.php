<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Bussines\AccountBusiness;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AccountService implements AccountServiceLocal
{
    private AccountBusiness $accountBusiness;

    public function __construct(AccountBusiness $accountBusiness)
    {
        $this->accountBusiness = $accountBusiness;
    }

    public function getListAllAccounts(User $user):Collection{
        return $this->accountBusiness->getListAllAccounts($user);
    }
    public function getListAllAccountFromLoggedUser():Collection{
        return $this->accountBusiness->getListAllAccountFromLoggedUser();
    }

    public function insertAccount(User $user,array $accountInfo):Model{
        return $this->accountBusiness->insertAccount($user,$accountInfo);
    }
    public function updateAccount(int $id,array $accountInfo):Model{
        return $this->accountBusiness->updateAccount($id,$accountInfo);
    }
    public function deleteAccount(int $id):bool{
        return $this->accountBusiness->deleteAccount($id);
    }
    public function getAccountById(int $id): Model
    {
        return $this->accountBusiness->getAccountById($id);
    }
}
