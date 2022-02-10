<?php

namespace App\Modules\Account\Repository;


use App\Models\Account;
use App\Models\CreditCard;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CreditCardRepository implements CreditCardRepositoryInterface
{
    public function getCreditCardsByAccountId(int $accountId):Collection
    {
        return Account::find($accountId)->creditCards()->orderBy('name', 'ASC')->get();
    }

    public function getCreditCardById(int $creditCardId):?CreditCard
    {
        return CreditCard::find($creditCardId);
    }

    public function saveCreditCard(int $accountId, array $creditCardData):CreditCard
    {
        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->account_id = $accountId;
        $creditCard->save();
        return $creditCard;
    }

    public function updateCreditCard(int $creditCardId, array $creditCardData):CreditCard
    {
        $creditCard = CreditCard::find($creditCardId);
        $creditCard->fill($creditCardData);
        $creditCard->update();
        return $creditCard;
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
