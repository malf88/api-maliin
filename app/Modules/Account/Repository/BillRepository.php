<?php

namespace App\Modules\Account\Repository;

use App\Models\Account;
use App\Models\Bill;
use App\Modules\Account\Impl\BillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;


class BillRepository implements BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId):Collection
    {
        return Account::find($accountId)->bills();
    }

    public function saveBill(int $accountId,array $billData):Bill
    {
        $bill = new Bill();
        $bill->fill($billData);
        $bill->account_id = $accountId;

        $bill->save();
        return $bill;

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

    public function getChildBill(int $billParentId):Collection
    {
        return Bill::where('bill_parent_id',$billParentId)
                    ->orWhere('id',$billParentId)
                    ->orderBy('created_at','ASC')
                    ->get();
    }

    public function deleteBill(int $billId):bool
    {

    }
}
