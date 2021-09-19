<?php

namespace App\Modules\Account\Impl;


use App\Models\CreditCard;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface CreditCardRepositoryInterface
{
    public function getCreditCardsByAccountId(int $accountId):Collection;
    public function getCreditCardById(int $creditCardId):?CreditCard;
    public function updateCreditCard(int $creditCardId, array $creditCardData):CreditCard;
    public function saveCreditCard(int $accountId, array $creditCardData):CreditCard;
    public function deleteCreditCard(int $creditCardId):bool;
    public function getInvoicesByCreditCard(int $creditCardId):Collection;
}
