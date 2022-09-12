<?php

namespace App\Modules\Account\Impl;


use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\CreditCardDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface CreditCardRepositoryInterface
{
    public function getCreditCardsByAccountId(int $accountId):Collection;
    public function getCreditCardById(int $creditCardId):?CreditCardDTO;
    public function updateCreditCard(int $creditCardId, CreditCardDTO $creditCardData):CreditCardDTO;
    public function saveCreditCard(int $accountId, CreditCardDTO $creditCardData):CreditCardDTO;
    public function deleteCreditCard(int $creditCardId):bool;
    public function getInvoicesByCreditCard(int $creditCardId):Collection;
    public function deleteInvoiceFromCreditCardId(int $creditCardId):void;
}
