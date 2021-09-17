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
        private BillRepositoryInterface $billRepository
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
                        ->invoices;
        $invoices->each(function($item,$key){
            $item->bills = Bill::where('credit_card_id',$item->credit_card_id)
                            ->whereBetween('date',[$item->start_date,$item->end_date])
                            ->get();
            $item->bills = $this->prepareBills($item->bills);
            $item->makeVisible(['bills']);
        });
        return $invoices;
    }

    private function prepareBills(Collection $bills):Collection
    {
        $bills->each(function($bill,$key){
            if($bill->bill_parent_id != null){
                $bill->bill_parent = $this->billRepository->getChildBill($bill->id,$bill->bill_parent_id);
            }else{
                $bill->load('bill_parent');
            }
            $bill->makeVisible(['bill_parent']);
        });
        return $bills;
    }
}
