<?php

namespace App\Modules\Account\Repository;


use App\Models\Account;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CreditCardRepository implements CreditCardRepositoryInterface
{
    private function makeDTO(CreditCard $creditCard):CreditCardDTO
    {
        $creditCardDTO = new CreditCardDTO($creditCard->toArray());
        $creditCardDTO->account = $creditCard->account;
        return $creditCardDTO;
    }

    public function getCreditCardsByAccountId(int $accountId):Collection
    {
        return Account::find($accountId)->creditCards()->orderBy('name', 'ASC')->get();
    }

    public function getCreditCardById(int $creditCardId):?CreditCardDTO
    {
        return $this->makeDTO(CreditCard::find($creditCardId));
    }

    public function saveCreditCard(int $accountId, CreditCardDTO $creditCardData):CreditCardDTO
    {
        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData->toArray());
        $creditCard->account_id = $accountId;
        $creditCard->save();
        return $this->makeDTO($creditCard);
    }

    public function updateCreditCard(int $creditCardId, CreditCardDTO $creditCardData):CreditCardDTO
    {
        $creditCard = CreditCard::find($creditCardId);
        $creditCard->fill($creditCardData->toArray());
        $creditCard->update();

        return $this->makeDTO($creditCard);
    }

    public function deleteInvoiceFromCreditCardId(int $creditCardId):void
    {
        Invoice::where('credit_card_id', $creditCardId)
            ->whereNull('pay_day')->delete();
    }

    public function deleteCreditCard(int $creditCardId):bool
    {
        $creditCard = CreditCard::find($creditCardId);
        return $creditCard->delete();
    }

    public function getInvoicesByCreditCard(int $creditCardId):Collection
    {
        $creditCard = CreditCard::find($creditCardId);
        return $creditCard->invoices;
    }


}
