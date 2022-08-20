<?php

namespace App\Modules\Account\Impl\Business;

use App\Abstracts\DTOAbstract;
use Illuminate\Support\Collection as BaseCollection;
use App\Modules\Account\DTO\BillDTO;
use App\Modules\Account\Services\BillPdfService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BillBusinessInterface
{
    public function getBillsByAccount(int $accountId):Collection;
    public function getBillsByAccountNormalized(int $accountId):LengthAwarePaginator;
    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator;
    public function insertBill(int $accountId,BillDTO $billData):DTOAbstract|Collection;
    public function getBillById(int $billId):Model;
    public function updateBill(int $billId,BillDTO $billData):DTOAbstract|BaseCollection;
    public function deleteBill(int $billId):bool;
    public function getBillsByAccountBetween(int $accountId,array $rangeDate):Collection;
    public function getPeriodWithBill(int $accountId):Collection;
    public function generatePdfByPeriod(BillPdfService $billPdfService,int $accountId,array $period):void;

}
