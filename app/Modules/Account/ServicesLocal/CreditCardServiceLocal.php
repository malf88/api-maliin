<?php

namespace App\Modules\Account\ServicesLocal;

 use Illuminate\Database\Eloquent\Collection;
 use Illuminate\Database\Eloquent\Model;

 interface CreditCardServiceLocal
{
     public function getListCreditCardByAccount(int $accountId):Collection;
     public function getCreditCardbyId(int $creditCardId):Model;
     public function insertCreditCard(int $accountId, array $creditCardData):Model;
     public function updateCreditCard(int $creditCardId, array $creditCardData):Model;
     public function removeCreditCard(int $creditCardId):bool;
     public function getInvoicesByCreditCard(int $creditCardId):Collection;
     public function getInvoicesWithBillByCreditCard(int $creditCardId):Collection;
}
