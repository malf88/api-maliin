<?php

namespace App\Modules\Account\Repository;

use App\Models\Account;
use App\Models\Bill;
use App\Modules\Account\Impl\BillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class BillRepository implements BillRepositoryInterface
{
    public function getBillsByAccount(int $accountId, bool $paginate = false):Collection|LengthAwarePaginator
    {
        if($paginate){
            return Bill::where('account_id',$accountId)->paginate(config('app.paginate'));
        }else{
            return Bill::where('account_id',$accountId)->get();
        }

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

//    public function getChildBill(int $billParentId):Collection
//    {
//        return Bill::where('bill_parent_id',$billParentId)
//                    ->orWhere('id',$billParentId)
//                    ->orderBy('created_at','ASC')
//                    ->get();
//    }

    public function deleteBill(int $billId):bool
    {
        $bill = Bill::find($billId);
        return $bill->delete();
    }
}
