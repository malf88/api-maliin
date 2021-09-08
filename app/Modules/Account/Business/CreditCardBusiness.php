<?php

namespace App\Modules\Account\Business;

use App\Models\Account;
use App\Models\CreditCard;
use App\Models\User;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use function PHPUnit\Framework\throwException;

class CreditCardBusiness
{
    private CreditCardRepositoryInterface $creditCardRepository;
    private AccountBusiness $accountBusiness;
    public function __construct(
        CreditCardRepositoryInterface $creditCardRepository,
        AccountBusiness $accountBusiness
    )
    {
        $this->accountBusiness = $accountBusiness;
        $this->creditCardRepository = $creditCardRepository;
    }

    public function getListCreditCardByAccount(int $accountId):Collection
    {
        if($this->accountBusiness->userHasAccount(Auth::user(),$accountId)){
            return $this->creditCardRepository->getCreditCardsByAccountId($accountId);
        }else{
            throw new ItemNotFoundException("Conta não encontrada");
        }

    }
    public function getCreditCardById(int $creditCardId):CreditCard
    {
         $creditCard = $this->creditCardRepository->getCreditCardById($creditCardId);

         if($creditCard != null && $this->userHasCreditCard(Auth::user(),$creditCard)){
             return $creditCard;
         }else{
             throw new ItemNotFoundException("Registro não encontrado");
         }

    }

    public function insertCreditCard(int $accountId, array $creditCardData):CreditCard
    {
        if($this->accountBusiness->userHasAccount(Auth::user(),$accountId)){
            return $this->creditCardRepository->saveCreditCard($accountId,$creditCardData);
        }else{
            throw new ItemNotFoundException('Não foi encontrada a conta informada');
        }
    }

    public function updateCreditCard(int $creditCardId, array $creditCardData):CreditCard
    {
        $creditCard = $this->getCreditCardById($creditCardId);
        if($creditCard != null && $this->userHasCreditCard(Auth::user(),$creditCard)){
            return $this->creditCardRepository->updateCreditCard($creditCardId,$creditCardData);
        }else{
            throw new ItemNotFoundException('Registro não encontrado');
        }
    }

    public function removeCreditCard(int $creditCardId):bool
    {
        $creditCard = $this->getCreditCardById($creditCardId);
        if($creditCard != null &&  $this->userHasCreditCard(Auth::user(),$creditCard)){
            return $this->creditCardRepository->deleteCreditCard($creditCardId);
        }else{
            throw new ItemNotFoundException("Registro não encontrado");
        }

    }

    public function userHasCreditCard(User $user, CreditCard $creditCard):bool
    {
        return $creditCard->account->user->id == $user->id;
    }

}
