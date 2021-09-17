<?php

namespace App\Modules\Account\ServicesLocal;

 use App\Models\CreditCard;
 use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Model;

 interface InvoiceServiceLocal
{
     public function getInvoiceByCreditCardAndDate(int $creditCardId,Carbon $date):Model|null;
     public function createInvoiceForCreditCardByDate(CreditCard $creditCard, Carbon $date):Model;

}
