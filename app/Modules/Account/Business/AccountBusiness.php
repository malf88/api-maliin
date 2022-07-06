<?php

namespace App\Modules\Account\Business;

use App\Exceptions\ExistsException;
use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\UserBusinessInterface;
use App\Modules\Account\Jobs\ShareAccountEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountBusiness implements AccountBusinessInterface
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly UserBusinessInterface $userBusiness
    )
    {
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
        });
        return $accountList;
    }

    /**
     * @param int $id
     * @return Account
     */
    public function getAccountById(int $id):Account
    {
        if(!Auth::user()->userHasAccount($id))
            throw new ItemNotFoundException('Registro não encontrado');

        return $this->prepareAccount($this->accountRepository->getAccountById($id));
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getListAllAccounts(User $user):Collection
    {
        return $this->prepareListAccount($this->accountRepository->getAccountFromUser($user));
    }

    /**
     * @return Collection
     */
    public function getListAllAccountFromLoggedUser():Collection
    {
        $user = Auth::user();
        return $this->prepareListAccount($this->accountRepository->getAccountFromUser($user));
    }

    /**
     * @param User $user
     * @param array $accountInfo
     * @return Model
     */
    public function insertAccount(User $user,array $accountInfo):Model
    {
        return $this->accountRepository->saveAccount($user,$accountInfo);
    }

    /**
     * @param int $id
     * @param array $accountInfo
     * @return Model
     */
    public function updateAccount(int $id,array $accountInfo):Model
    {
        if(!Auth::user()->userIsOwnerAccount($id))
            throw new ItemNotFoundException("Registro não encontrado");

        return $this->accountRepository->updateAccount($id,$accountInfo);

    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteAccount(int $id):bool
    {
        if (!Auth::user()->userIsOwnerAccount($id))
            throw new ItemNotFoundException("Registro não encontrado");

        return $this->accountRepository->deleteAccount($id);

    }
    public function addUserToAccount(int $accountId,int $userId):bool
    {
        if (!Auth::user()->userIsOwnerAccount($accountId))
            throw new ItemNotFoundException("Registro não encontrado");

        if ($this->accountRepository->userHasSharedAccount($accountId,$userId))
            throw new ExistsException("Usuário já está associado a conta");

        return $this->saveUserToAccount($accountId, $userId);
    }

    public function removeUserToAccount(int $accountId,int $userId):bool
    {
        if (!Auth::user()->userIsOwnerAccount($accountId))
            throw new ItemNotFoundException("Registro não encontrado");

        if (!$this->accountRepository->userHasSharedAccount($accountId,$userId))
            throw new ExistsException("Usuário não está associado a conta");

        return $this->accountRepository->removeUserToAccount($accountId, $userId);

    }

    public function addUserToAccountByEmail(int $accountId, string $email):bool
    {
        if (!Auth::user()->userIsOwnerAccount($accountId))
            throw new NotFoundHttpException("Registro não encontrado");

        $user = $this->userBusiness->findUserOrGenerateByEmail($email);

        if ($this->accountRepository->userHasSharedAccount($accountId,$user->id))
            throw new ExistsException("Usuário já está associado a conta");

        return $this->saveUserToAccount($accountId, $user->id);
    }

    private function saveUserToAccount(int $accountId, int $userId):bool
    {
        $insertResult = $this->accountRepository->addUserToAccount($accountId, $userId);
        if($insertResult){
            ShareAccountEmail::dispatch($accountId, $userId)->onQueue('maliin');
            return $insertResult;
        }
        return false;
    }
}
