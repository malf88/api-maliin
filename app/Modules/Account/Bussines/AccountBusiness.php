<?php

namespace App\Modules\Account\Bussines;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Respository\AccountRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Pure;
use LogicException;

class AccountBusiness
{
    private AccountRepository $accountRepository;
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getListAllAccounts(User $user):Collection{
        return $this->accountRepository->getAccountFromUser($user);
    }
    public function getListAllAccountFromLoggedUser():Collection{
        $user = Auth::user();
        return $this->accountRepository->getAccountFromUser($user);
    }

    public function insertAccount(User $user,array $accountInfo):Model{
        return $this->accountRepository->saveAccount($user,$accountInfo);
    }

    public function updateAccount(int $id,array $accountInfo):Model{
        return $this->accountRepository->updateAccount($id,$accountInfo);

    }
    public function deleteAccount(int $id):bool{
        return $this->accountRepository->deleteAccount($id);
    }
}
