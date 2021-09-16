<?php

namespace App\Modules\Account\Impl;

use App\Models\Invoice;
use Carbon\Carbon;

interface InvoiceRepositoryInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Invoice|null;
    public function insertInvoice(array $invoiceData):Invoice;
}
