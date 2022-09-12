<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\DTO\InvoiceDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CreditCardBusinessInterface
{
    public function getListCreditCardByAccount(int $accountId):Collection;
    public function getCreditCardById(int $creditCardId):CreditCardDTO;
    public function insertCreditCard(int $accountId, CreditCardDTO $creditCardData):CreditCardDTO;
    public function updateCreditCard(int $creditCardId, CreditCardDTO $creditCardData):CreditCardDTO;
    public function removeCreditCard(int $creditCardId):bool;
    public function getInvoicesByCreditCard(int $creditCardId):Collection;
    public function generateInvoiceByBill(int $creditCardId,string $billDate):InvoiceDTO    ;
    public function getInvoicesWithBillByCreditCard(int $creditCardId):Collection;
    public function getBillByCreditCardId(int $creditCardId):Collection;
    public function regenerateInvoicesByCreditCard(int $creditCardId):void;
    public function isCreditCardValid(int $creditCardId):bool;
}
