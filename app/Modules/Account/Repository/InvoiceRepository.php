<?php

namespace App\Modules\Account\Repository;

use App\Models\Bill;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\InvoiceDTO;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Traits\RepositoryTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\True_;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    use RepositoryTrait;
    public function __construct(
    )
    {
    }

    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): InvoiceDTO|null
    {
        $invoice = CreditCard::find($creditCardId)
            ->invoices()
            ->where('start_date','<=',$date)
            ->where('end_date','>=',$date)
            ->first();
        if($invoice != null)
            return new InvoiceDTO($invoice->toArray());
        return null;
    }

    public function insertInvoice(InvoiceDTO $invoiceData): InvoiceDTO
    {
        $invoice = new Invoice();
        $invoice->fill($invoiceData->toArray());
        $invoice->save();
        return new InvoiceDTO($invoice->toArray());
    }

    public function getInvoicesWithBills(int $creditCardId):Collection
    {
        $invoices = CreditCard::find($creditCardId)
                        ->invoices()
                        ->orderBy('start_date','ASC')
                        ->get();
        $invoices->each(function($item,$key){
            $item->bills = Bill::select(DB::raw('description,
                            amount,
                            date,
                            \''.$item->due_date.'\' as due_date,
                            pay_day,
                            barcode,
                            bill_parent_id,
                            category_id,
                            account_id,
                            id,
                            credit_card_id,
                            portion'))
                            ->with(['category','credit_card'])
                            ->where('credit_card_id',$item->credit_card_id)
                            ->whereBetween('date',[$item->start_date,$item->end_date])
                            ->orderBy('date','ASC')
                            ->get();
            $item->total_balance = $item->bills->sum('amount');
            $item->makeVisible(['bills','total_balance']);
        });
        return $invoices;
    }


    public function getInvoiceWithBills(int $invoiceId): InvoiceDTO
    {
        $invoice = Invoice::with('credit_card')->find($invoiceId);
        $invoice->bills = Bill::select(
            DB::raw('description,
                            amount,
                            date,
                            \''.$invoice->due_date.'\' as due_date,
                            pay_day,
                            barcode,
                            bill_parent_id,
                            category_id,
                            account_id,
                            id,
                            credit_card_id,
                            portion'))
            ->with(['category','credit_card'])
            ->where('credit_card_id',$invoice->credit_card_id)
            ->whereBetween('date',[$invoice->start_date,$invoice->end_date])
            ->orderBy('date','ASC')
            ->get();
        $invoice->total_balance = $invoice->bills->sum('amount');
        $invoice->makeVisible(['total_balance','bills']);
        return new InvoiceDTO($invoice->toArray());
    }

    public function payBillForInvoice(InvoiceDTO $invoice):InvoiceDTO
    {
        try{
            $invoice
                ->bills
                ->each(function($item,$key){
                    unset($item->bill_parent);
                    $item->pay_day = Carbon::now();
                    $item->save();
                    $item->refresh();
                });
            return $invoice;
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function saveInvoice(InvoiceDTO $invoiceDTO):InvoiceDTO
    {
        $invoice = Invoice::find($invoiceDTO->id);
        $invoice->fill($invoiceDTO->toArray());
        $invoice->save();
        $invoice->refresh();
        return new InvoiceDTO($invoice->toArray());
    }
    public function getInvoice(int $invoiceId): InvoiceDTO
    {
        return new InvoiceDTO(Invoice::find($invoiceId)->toArray());
    }
}
