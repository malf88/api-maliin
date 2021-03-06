<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\CreditCard;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface InvoiceBusinessInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Model|null;
    public function createInvoiceForCreditCardByDate(CreditCard $creditCard, Carbon $date):Model;
    public function getInvoiceWithBills(int $invoiceId):Model;
    public function payInvoice(int $invoiceId):Model;
    public function getInvoiceWithBillsInPDF(BillPdfInterface $billPdfService,int $invoiceId, bool $normalize = false):void;
}
