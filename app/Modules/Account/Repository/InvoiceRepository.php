<?php

namespace App\Modules\Account\Repository;

use App\Models\Bill;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
    )
    {
    }

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

    public function getInvoicesWithBills(int $creditCardId):Collection
    {
        $invoices = CreditCard::find($creditCardId)
                        ->invoices()
                        ->orderBy('start_date','ASC')
                        ->get();
        $invoices->each(function($item,$key){
            $item->bills = Bill::where('credit_card_id',$item->credit_card_id)
                            ->whereBetween('date',[$item->start_date,$item->end_date])
                            ->orderBy('date','ASC')
                            ->get();
            //$item->bills = $this->prepareBills($item->bills);
            $item->total_balance = $item->bills->sum('amount');
            $item->makeVisible(['bills','total_balance']);
        });
        return $invoices;
    }


    public function getInvoiceWithBills(int $invoiceId): Invoice
    {
        $invoice = Invoice::find($invoiceId);
        $invoice->bills = Bill::where('credit_card_id',$invoice->credit_card_id)
            ->whereBetween('date',[$invoice->start_date,$invoice->end_date])
            ->orderBy('date','ASC')
            ->get();
        //$invoice->bills = $this->prepareBills($invoice->bills);
        $invoice->total_balance = $invoice->bills->sum('amount');
        $invoice->makeVisible(['total_balance']);
        return $invoice;
    }


    public function getInvoice(int $invoiceId): Invoice
    {
        return Invoice::find($invoiceId);
    }
}
