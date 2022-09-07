<?php

namespace App\Modules\Account\Repository;

use App\Models\Bill;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\BillDTO;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Traits\RepositoryTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class BillRepository implements BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId):Collection
    {
        return $this->getBillsQuery($accountId)->orderBy('date','ASC')->get();
    }

    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator
    {
        return $this->getBillsQuery($accountId)->orderBy('date','ASC')->paginate(config('app.paginate'));
    }
    public function getTotalEstimated(Collection $bills):float
    {
        return $bills->sum('amount');
    }
    public function getTotalPaid(Collection $bills):float
    {
        return $bills->whereNotNull('pay_day')->sum('amount');
    }
    public function getTotalCashIn(Collection $bills):float
    {
        return $bills->where('amount','>=',0)->sum('amount');
    }
    public function getTotalCashOut(Collection $bills):float
    {
        return $bills->where('amount','<',0)->sum('amount');
    }
    public function getBillsByAccountWithRangeDate(int $accountId, array $rangeDate = null):Collection
    {
        return $this->getBillsQueryWithRangeDate($accountId,$rangeDate)->get();
    }

    public function getBillsByAccountWithRangeDatePaginate(int $accountId, array $rangeDate = null):LengthAwarePaginator
    {
        return $this->getBillsQueryWithRangeDate($accountId,$rangeDate)->paginate(config('app.paginate'));

    }
    private function getBillsQueryWithRangeDate(int $accountId, array $rangeDate):Builder
    {
        return  $this->getQueryBill($accountId)
            ->whereBetween('due_date',$rangeDate)
            ->with(['category','credit_card'])
            ->union(
                    $this->getQueryInvoiceAsBill($accountId)
                         ->whereBetween('due_date',$rangeDate)
            )
            ->orderBy('date','ASC');
    }
    private function getBillsQuery(int $accountId,):Builder
    {
        return  $this->getQueryBill($accountId)
            ->orderBy('date','ASC')
                    ->union($this->getQueryInvoiceAsBill($accountId));

    }
    public function getMonthWithBill(int $accountId):Collection
    {
        $registros = DB::select("SELECT DISTINCT(EXTRACT(YEAR FROM  due_date)) as year,EXTRACT(MONTH FROM  due_date) as month
                                    FROM maliin.bills
                                    WHERE due_date IS NOT NULL AND account_id = $accountId
                                    UNION
                                    SELECT DISTINCT(EXTRACT(YEAR FROM  due_date)),EXTRACT(MONTH FROM  due_date)
                                    FROM maliin.invoices
                                        JOIN maliin.credit_cards using(id)
                                    WHERE account_id = $accountId
                                    ORDER BY 1 DESC ,2 DESC
                                    ");
        return Collection::make($registros);
    }
    public function getQueryBill(int $accountId):Builder
    {
        return $bill =  Bill::select(DB::raw(
                            "id,
                            account_id,
                            description,
                            amount,
                            date,
                            due_date,
                            pay_day,
                            credit_card_id,
                            category_id,
                            barcode,
                            portion,
                            bill_parent_id,
                            'false' as is_credit_card"
        ))
            ->where('account_id',$accountId)
            ->where('credit_card_id');
    }

    public function getQueryInvoiceAsBill(int $accountId):Builder
    {
        return Invoice::select(DB::raw("invoices.id,
                                    (SELECT id FROM maliin.accounts WHERE accounts.id = credit_cards.account_id),
                                    'Fatura do cartão de crédito '||credit_cards.name,
                                    (SELECT
                                        SUM(amount)
                                     FROM maliin.bills
                                     WHERE
                                        credit_card_id = invoices.credit_card_id AND
                                        date between invoices.start_date AND
                                        invoices.end_date AND
                                        bills.deleted_at IS NULL
                                    ) as amount,
                                    invoices.start_date,
                                    invoices.due_date,
                                    invoices.pay_day,
                                    invoices.credit_card_id,
                                    null,
                                    '',
                                    null,
                                    null,
                                    true")
        )
            ->join('credit_cards','invoices.credit_card_id','=','credit_cards.id')
            ->where('account_id',$accountId)
            ->orderBy('start_date','ASC');
    }

    public function saveBill(int $accountId,BillDTO $billDTO):BillDTO
    {
        $bill = new Bill($billDTO->toArray());

        $bill->account_id = $accountId;
        $bill->save();
        return new BillDTO($bill->toArray());

    }
    public function getCategory(Bill $bill):Category|null
    {
        return $bill->category;
    }

    public function getCreditCard(Bill $bill):CreditCard|null
    {
        return $bill->credit_card;
    }
    public function getChildBill(int $billId, int $billParentId = null):Collection
    {
        $queryFindChild = Bill::select(
            DB::raw(
                'filho.description,
                        filho.amount,
                        filho.date,
                        COALESCE(filho.due_date,
                            (SELECT invoices.due_date
                            FROM maliin.invoices
                            WHERE
                                filho.date BETWEEN start_date AND end_date AND
                                invoices.credit_card_id = filho.credit_card_id)
                        ) as due_date,
                        filho.pay_day,
                        filho.barcode,
                        filho.bill_parent_id,
                        filho.category_id,
                        filho.account_id,
                        filho.id,
                        filho.credit_card_id,
                        filho.portion'))
            ->leftJoin('maliin.bills as filho',function($join) use($billId){
                $join->on('filho.bill_parent_id','=','bills.id')->orOn('bills.id','=','filho.id');
            })
            ->with(['category','credit_card'])
            ->where('filho.id','<>',$billId)
            ->whereNull('filho.deleted_at')
            ->orderBy('filho.due_date','ASC');
        if($billParentId != null){
            $queryFindChild->where('bills.id',$billParentId);
        }else{
            $queryFindChild->where('bills.id',$billId);
        }
        return $queryFindChild->get();
    }
    public function getBillById(int $billId):Bill
    {
        return Bill::with(['category','credit_card'])->find($billId);
    }

    public function updatePayDayBill(int $accountId, BillDTO $billData): BillDTO
    {
        $bill = Bill::find($accountId);
        $bill->pay_day = $billData->pay_day;
        $bill->update();
        return new BillDTO($bill->toArray());
    }
    public function updateBill(int $billId, BillDTO $billData): BillDTO
    {
        $bill = Bill::find($billId);
        $billData->account_id = $bill->account_id;
        $bill->fill($billData->toArray());
        $bill->update();
        return new BillDTO($bill->toArray());
    }

    public function getBillsByCreditCardId(int $creditCardId):Collection
    {
        return Bill::where('credit_card_id', $creditCardId)->get();
    }
    public function deleteBill(int $billId):bool
    {
        $bill = Bill::find($billId);
        return $bill->delete();
    }
}
