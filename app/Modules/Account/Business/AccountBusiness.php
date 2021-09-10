<?php

namespace App\Modules\Account\Business;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Repository\AccountRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use JetBrains\PhpStorm\Pure;
use LogicException;
use PhpParser\Node\Expr\AssignOp\Mod;

class AccountBusiness
{
    private AccountRepositoryInterface $accountRepository;
    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param Account $account
     * @return Account
     */
    private function prepareAccount(Account $account):Account{
        $account->makeVisible(['total_balance','total_estimated','bills']);

        $account->setAttribute('total_balance',$account->bills()->whereNotNull('pay_day')->sum('amount'));
        $account->setAttribute('total_estimated',$account->bills()->sum('amount'));
        return $account;
    }

    /**
     * @param Collection $accountList
     * @return Collection
     */
    private function prepareListAccount(Collection $accountList):Collection{

        $accountList->each(function($item,$index){
            $item->makeVisible(['total_balance','total_estimated','bills']);
            $item->setAttribute('total_balance',$item->bills()->whereNotNull('pay_day')->sum('amount'));
            $item->setAttribute('total_estimated',$item->bills()->sum('amount'));
            //$item->load(['bills']);
        });
        return $accountList;
    }

    /**
     * @param int $id
     * @return Account
     */
    public function getAccountById(int $id):Account{
        if($this->userHasAccount(Auth::user(),$id)){
            return $this->prepareAccount($this->accountRepository->getAccountById($id));
        }else{
            throw new ItemNotFoundException('Registro não encontrado');
        }
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getListAllAccounts(User $user):Collection{
        return $this->prepareListAccount($this->accountRepository->getAccountFromUser($user));
    }

    /**
     * @return Collection
     */
    public function getListAllAccountFromLoggedUser():Collection{
        $user = Auth::user();
        return $this->prepareListAccount($this->accountRepository->getAccountFromUser($user));
    }

    /**
     * @param User $user
     * @param array $accountInfo
     * @return Model
     */
    public function insertAccount(User $user,array $accountInfo):Model{
        return $this->accountRepository->saveAccount($user,$accountInfo);
    }

    /**
     * @param int $id
     * @param array $accountInfo
     * @return Model
     */
    public function updateAccount(int $id,array $accountInfo):Model{
        if($this->userHasAccount(Auth::user(),$id)){
            return $this->accountRepository->updateAccount($id,$accountInfo);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteAccount(int $id):bool{
        if ($this->userHasAccount(Auth::user(), $id)) {
            return $this->accountRepository->deleteAccount($id);
        } else {
            throw new ItemNotFoundException("Registro não encontrado");
        }
    }

    /**
     * Método que verifica se o registro que está sendo acessado é do usuário autenticado.
     * @param User $user
     * @param int $id
     * @return bool
     */
    public function userHasAccount(User $user,int $id):bool{

        return $user->accounts()->find($id) != null;
    }
}
