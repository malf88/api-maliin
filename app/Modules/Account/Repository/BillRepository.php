<?php

namespace App\Modules\Account\Repository;

use App\Models\Bill;
use App\Models\Invoice;
use App\Modules\Account\Impl\BillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class BillRepository implements BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId, bool $paginate = false, array $rangeDate = null):Collection|LengthAwarePaginator
    {
        if($paginate){
            return $this->getBillsQuery($accountId, $rangeDate)->paginate(config('app.paginate'));
        }else{
            return $this->getBillsQuery($accountId, $rangeDate)->get();
        }
    }

    private function getBillsQuery(int $accountId, array $rangeDate = null):Builder
    {
        $invoices = Invoice::select(DB::raw("invoices.id,
                                    'Fatura do cartão de crédito '||credit_cards.name,
                                    (SELECT
                                        SUM(amount)
                                     FROM maliin.bills
                                     WHERE
                                        credit_card_id = invoices.credit_card_id AND
                                        date between invoices.start_date AND
                                        invoices.end_date
                                    ) as amount,
                                    invoices.start_date,
                                    invoices.due_date,
                                    invoices.pay_day,
                                    invoices.credit_card_id,
                                    null,
                                    '',
                                    null,
                                    true")
                )
                ->join('credit_cards','invoices.credit_card_id','=','credit_cards.id')
                ->where('account_id',$accountId);



        $bill =  Bill::select(DB::raw("id,
                            description,
                            amount,
                            date,
                            due_date,
                            pay_day,
                            credit_card_id,
                            category_id,
                            barcode,
                            bill_parent_id,
                            'false' as is_credit_card")
                    )
                    ->where('account_id',$accountId)
                    ->where('credit_card_id');

            if( $rangeDate != null ){
                $invoices->whereBetween('due_date',$rangeDate);
                $bill->whereBetween('due_date',$rangeDate);
            }
            return $bill->union($invoices);
    }

    public function saveBill(int $accountId,array $billData):Bill
    {
        $bill = new Bill();
        $bill->fill($billData);
        $bill->account_id = $accountId;
        $bill->save();
        return $bill;

    }
    public function getChildBill(int $billId, int $billParentId):Collection
    {
        return Bill::select('filho.*')
            ->leftJoin('maliin.bills as filho',function($join) use($billId){
                $join->on('filho.bill_parent_id','=','bills.id')->orOn('bills.id','=','filho.id');
            })
            ->where('bills.id',$billParentId)
            ->where('filho.id','<>',$billId)
            ->orderBy('filho.created_at','ASC')
            ->get();
    }
    public function getBillById(int $billId):Bill
    {
        return Bill::find($billId);
    }

    public function updateBill(int $accountId, array $billData): Bill
    {
        $bill = Bill::find($accountId);
        $bill->fill($billData);
        $bill->update();
        return $bill;
    }

    public function deleteBill(int $billId):bool
    {
        $bill = Bill::find($billId);
        return $bill->delete();
    }
}
