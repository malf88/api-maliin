<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\CreditCard;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\DTO\InvoiceDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface InvoiceBusinessInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): InvoiceDTO|null;
    public function createInvoiceForCreditCardByDate(CreditCardDTO $creditCard, Carbon $date):InvoiceDTO;
    public function getInvoiceWithBills(int $invoiceId):InvoiceDTO;
    public function getInvoiceWithBillsNormalized(int $invoiceId):InvoiceDTO;
    public function payInvoice(int $invoiceId):InvoiceDTO;
    public function getInvoiceWithBillsInPDF(BillPdfInterface $billPdfService,int $invoiceId):void;
}
