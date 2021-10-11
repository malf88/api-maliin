<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class CreditCardBusiness implements CreditCardBusinessInterface
{

    public function __construct(
        private CreditCardRepositoryInterface $creditCardRepository,
        private InvoiceBusinessInterface $invoiceBusiness
    )
    {

    }

    public function getListCreditCardByAccount(int $accountId):Collection
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->creditCardRepository->getCreditCardsByAccountId($accountId);
        }else{
            throw new ItemNotFoundException("Conta não encontrada");
        }

    }
    public function getCreditCardById(int $creditCardId):Model
    {
         $creditCard = $this->creditCardRepository->getCreditCardById($creditCardId);

         if($creditCard != null && Auth::user()->userHasAccount($creditCard->account_id)){
             return $creditCard;
         }else{
             throw new ItemNotFoundException("Registro não encontrado");
         }

    }

    public function insertCreditCard(int $accountId, array $creditCardData):Model
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->creditCardRepository->saveCreditCard($accountId,$creditCardData);
        }else{
            throw new ItemNotFoundException('Não foi encontrada a conta informada');
        }
    }

    public function updateCreditCard(int $creditCardId, array $creditCardData):Model
    {
        $this->getCreditCardById($creditCardId);
        return $this->creditCardRepository->updateCreditCard($creditCardId,$creditCardData);

    }

    public function removeCreditCard(int $creditCardId):bool
    {
        $this->getCreditCardById($creditCardId);
        return $this->creditCardRepository->deleteCreditCard($creditCardId);
    }

    public function getInvoicesByCreditCard(int $creditCardId):Collection
    {
        $this->getCreditCardById($creditCardId);
        return $this->creditCardRepository->getInvoicesByCreditCard($creditCardId);
    }

    public function generateInvoiceByBill(int $creditCardId,string $billDate):Model
    {
        $creditCard = $this->getCreditCardById($creditCardId);
        return $this->invoiceBusiness->createInvoiceForCreditCardByDate($creditCard,Carbon::make($billDate));
    }

    public function getInvoicesWithBillByCreditCard(int $creditCardId):Collection
    {
        $this->getCreditCardById($creditCardId);
        return $this->invoiceBusiness->getInvoiceWithBill($creditCardId);
    }
}
