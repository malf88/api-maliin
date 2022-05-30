<?php

namespace App\Modules\Account\ServicesLocal;

 use Illuminate\Contracts\Pagination\LengthAwarePaginator;
 use Illuminate\Database\Eloquent\Collection;
 use Illuminate\Database\Eloquent\Model;

 interface BillServiceLocal
{
     public function getBillsByAccount(int $accountId):Collection;
     public function updateBill(int $billId,array $billData):Model|Collection;
     public function insertBill(int $accountId,$billData):Model|Collection;
     public function getBillById(int $billId):Model;
     public function updateChildBill(int $billId,array $billData):Collection;
     public function deleteBill(int $billId):bool;
     public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator;
     public function getBillsByAccountBetween(int $accountId,array $rangeDate):Collection;
     public function getPeriodWithBill(int $accountId):Collection;
     public function generatePdfByPeriod(int $accountId,array $rangeDate):void;
}
