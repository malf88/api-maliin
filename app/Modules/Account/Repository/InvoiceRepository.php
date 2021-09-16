<?php

namespace App\Modules\Account\Repository;

use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use Carbon\Carbon;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Invoice|null
    {
        return CreditCard::find($creditCardId)
            ->invoices()
            ->where('start_date','<=',$date)
            ->where('end_date','>=',$date)
            ->first();
    }

    public function insertInvoice(array $invoiceData): Invoice
    {
        $invoice = new Invoice();
        $invoice->fill($invoiceData);
        $invoice->save();
        return $invoice;
    }
}
