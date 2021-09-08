<?php

namespace App\Modules\Account\ServicesLocal;

 use App\Models\CreditCard;
 use Illuminate\Database\Eloquent\Collection;

 interface CreditCardServiceLocal
{
     public function getListCreditCardByAccount(int $accountId):Collection;
     public function getCreditCardbyId(int $creditCardId):CreditCard;
     public function insertCreditCard(int $accountId, array $creditCardData):CreditCard;
     public function updateCreditCard(int $creditCardId, array $creditCardData):CreditCard;
     public function removeCreditCard(int $creditCardId):bool;
}
