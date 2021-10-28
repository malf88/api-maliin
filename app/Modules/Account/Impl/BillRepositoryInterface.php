<?php

namespace App\Modules\Account\Impl;

use App\Models\Bill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId, bool $paginate = false):Collection|LengthAwarePaginator;
    public function getBillsByAccountWithRangeDate(int $accountId, array $rangeDate = null,bool $paginate = false):Collection|LengthAwarePaginator;
    public function saveBill(int $accountId,array $billData):Bill;
    public function updateBill(int $accountId,array $billData):Bill;
    public function getBillById(int $billId):Bill;
    public function getChildBill(int $billId, int $billParentId):Collection;
    public function deleteBill(int $billId):bool;
}
