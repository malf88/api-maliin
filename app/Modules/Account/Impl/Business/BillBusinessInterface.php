<?php

namespace App\Modules\Account\Impl\Business;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BillBusinessInterface
{
    public function getBillsByAccount(int $accountId):Collection;
    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator;
    public function insertBill(int $accountId,$billData):Model|Collection;
    public function getBillById(int $billId):Model;
    public function updateBill(int $billId,array $billData):Model|Collection;
    public function deleteBill(int $billId):bool;
    public function userHasBill(User $user, Model $bill):bool;

}
