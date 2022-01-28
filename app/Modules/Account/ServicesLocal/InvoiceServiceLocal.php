<?php

namespace App\Modules\Account\ServicesLocal;

 use App\Models\CreditCard;
 use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Collection;
 use Illuminate\Database\Eloquent\Model;

 interface InvoiceServiceLocal
{
     public function getInvoiceByCreditCardAndDate(int $creditCardId,Carbon $date):Model|null;
     public function createInvoiceForCreditCardByDate(Model $creditCard, Carbon $date):Model;
     public function payInvoiceAndBill(int $invoiceId):Model;
     public function getInvoiceWithBills(int $invoiceId):Model;
}
