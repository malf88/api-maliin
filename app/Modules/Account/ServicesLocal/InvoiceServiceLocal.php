<?php

namespace App\Modules\Account\ServicesLocal;

 use App\Modules\Account\Impl\Business\BillPdfInterface;
 use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Model;

 interface InvoiceServiceLocal
{
     public function getInvoiceByCreditCardAndDate(int $creditCardId,Carbon $date):Model|null;
     public function createInvoiceForCreditCardByDate(Model $creditCard, Carbon $date):Model;
     public function payInvoiceAndBill(int $invoiceId):Model;
     public function getInvoiceWithBills(int $invoiceId):Model;
     public function getInvoiceWithBillsInPDF(int $invoiceId, bool $normalize = false):void;

}
