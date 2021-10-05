<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CreditCardBusinessInterface
{
    public function getListCreditCardByAccount(int $accountId):Collection;
    public function getCreditCardById(int $creditCardId):Model;
    public function insertCreditCard(int $accountId, array $creditCardData):Model;
    public function updateCreditCard(int $creditCardId, array $creditCardData):Model;
    public function removeCreditCard(int $creditCardId):bool;
    public function getInvoicesByCreditCard(int $creditCardId):Collection;
    public function generateInvoiceByBill(int $creditCardId,string $billDate):Model;
    public function getInvoicesWithBillByCreditCard(int $creditCardId):Collection;
}
