<?php

namespace App\Modules\Account\Impl;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Invoice|null;
    public function insertInvoice(array $invoiceData):Invoice;
    public function getInvoicesWithBills(int $creditCardId):Collection;
}
