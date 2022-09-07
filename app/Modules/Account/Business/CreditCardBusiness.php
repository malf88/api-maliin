<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\DTO\InvoiceDTO;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use App\Traits\RepositoryTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreditCardBusiness implements CreditCardBusinessInterface
{
    use RepositoryTrait;

    public function __construct(
        private CreditCardRepositoryInterface $creditCardRepository,
        private InvoiceBusinessInterface $invoiceBusiness,
        private BillRepositoryInterface $billRepository
    )
    {

    }
    public function getListCreditCardByAccount(int $accountId):Collection
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->creditCardRepository->getCreditCardsByAccountId($accountId);
        }else{
            throw new NotFoundHttpException("Conta não encontrada");
        }

    }
    public function getCreditCardById(int $creditCardId):Model
    {
         $creditCard = $this->creditCardRepository->getCreditCardById($creditCardId);

         if($creditCard != null && Auth::user()->userHasAccount($creditCard->account_id)){
             return $creditCard;
         }else{
             throw new NotFoundHttpException("Registro não encontrado");
         }
    }

    public function insertCreditCard(int $accountId, array $creditCardData):Model
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->creditCardRepository->saveCreditCard($accountId,$creditCardData);
        }else{
            throw new NotFoundHttpException('Não foi encontrada a conta informada');
        }
    }

    public function updateCreditCard(int $creditCardId, array $creditCardData):Model
    {
        $this->getCreditCardById($creditCardId);
        $creditCard = $this->creditCardRepository->updateCreditCard($creditCardId,$creditCardData);
        $this->creditCardRepository->deleteInvoiceFromCreditCardId($creditCard->id);
        $bills = $this->billRepository->getBillsByCreditCardId($creditCard->id);
        try{
            $this->startTransaction();
            $bills->each(function($item) use($creditCard){
                $this->generateInvoiceByBill($creditCard->id, $item->date);
            });
            $this->commitTransaction();
        }catch (\Exception $e){
            $this->rollbackTransaction();
            throw $e;
        }
        return $creditCard;

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

    public function generateInvoiceByBill(int $creditCardId,string $billDate):InvoiceDTO
    {
        $creditCard = $this->getCreditCardById($creditCardId);
        return $this->invoiceBusiness->createInvoiceForCreditCardByDate($creditCard,Carbon::make($billDate));
    }

    public function getInvoicesWithBillByCreditCard(int $creditCardId):Collection
    {
        $this->getCreditCardById($creditCardId);
        return $this->invoiceBusiness->getInvoicesWithBill($creditCardId);
    }

}
