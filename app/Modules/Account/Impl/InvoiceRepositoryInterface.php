<?php

namespace App\Modules\Account\Impl;

use App\Models\Invoice;
use App\Modules\Account\DTO\InvoiceDTO;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): InvoiceDTO|null;
    public function insertInvoice(InvoiceDTO $invoiceData):InvoiceDTO;
    public function getInvoicesWithBills(int $creditCardId):Collection;
    public function getInvoice(int $invoiceId):InvoiceDTO;
    public function getInvoiceWithBills(int $invoiceId): InvoiceDTO;
    public function saveInvoice(InvoiceDTO $invoiceDTO):InvoiceDTO;
    public function payBillForInvoice(InvoiceDTO $invoice):InvoiceDTO;
}
