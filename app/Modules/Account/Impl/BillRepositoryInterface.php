<?php

namespace App\Modules\Account\Impl;

use App\Models\Bill;
use App\Models\Category;
use App\Models\CreditCard;
use App\Modules\Account\DTO\BillDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId):Collection;
    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator;
    public function getBillsByAccountWithRangeDate(int $accountId, array $rangeDate = null):Collection;
    public function getBillsByAccountWithRangeDatePaginate(int $accountId, array $rangeDate = null):LengthAwarePaginator;
    public function saveBill(int $accountId,BillDTO $billDTO):BillDTO;
    public function updateBill(int $billId,BillDTO $billData):BillDTO;
    public function updatePayDayBill(int $accountId, BillDTO $billData): BillDTO;
    public function getBillById(int $billId):Bill;
    public function getChildBill(int $billId, int $billParentId):Collection;
    public function deleteBill(int $billId):bool;
    public function getMonthWithBill(int $accountId):Collection;
    public function getTotalEstimated(Collection $bills):float;
    public function getTotalPaid(Collection $bills):float;
    public function getTotalCashIn(Collection $bills):float;
    public function getTotalCashOut(Collection $bills):float;
    public function getCategory(Bill $bill):Category|null;
    public function getCreditCard(Bill $bill):CreditCard|null;
    public function getBillsByCreditCardId(int $creditCardId):Collection;
    public function getBillWithPayDayNullByCreditCardId(int $creditCardId):Collection;

}
