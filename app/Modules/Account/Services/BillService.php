<?php

namespace App\Modules\Account\Services;

use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BillService implements BillServiceLocal
{
    private BillBusinessInterface $billBusiness;

    public function __construct(BillBusinessInterface $billBusiness)
    {
        $this->billBusiness = $billBusiness;
    }

    public function getBillsByAccount(int $accountId): Collection
    {
        return $this->billBusiness->getBillsByAccount($accountId);
    }

    public function updateBill(int $billId, array $billData): Model|Collection
    {
        return $this->billBusiness->updateBill($billId,$billData);
    }

    public function insertBill(int $accountId, $billData): Model|Collection
    {
        return $this->billBusiness->insertBill($accountId,$billData);
    }

    public function getBillById(int $billId): Model
    {
        return $this->billBusiness->getBillById($billId);
    }

    public function updateChildBill(int $billId, array $billData): Collection
    {
        // TODO: Implement updateChildBill() method.
    }

    public function deleteBill(int $billId): bool
    {
        return $this->billBusiness->deleteBill($billId);
    }

    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator
    {
        return $this->billBusiness->getBillsByAccountPaginate($accountId);
    }
}
