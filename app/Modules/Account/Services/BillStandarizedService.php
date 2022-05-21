<?php

namespace App\Modules\Account\Services;

use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BillStandarizedService implements \App\Modules\Account\Impl\Business\BillStandarizedInterface
{
    public function __construct(private BillRepositoryInterface $billRepository)
    {

    }

    public function normalizeListBills(Collection|LengthAwarePaginator $bills):Collection|LengthAwarePaginator
    {
        $bills->each(function($item,$key){
            $item = $this->normalizeBill($item);
        });
        return $bills;
    }

    public function normalizeBill(Model $bill):Model
    {
        $bill = $this->findChildBill($bill);
        return $bill;
    }

    private function findChildBill(Model $bill):Model
    {

        $bill->bill_parent = $this->billRepository->getChildBill($bill->id,$bill->bill_parent_id);

        $bill->bill_parent->each(function($item,$key){
            $item->makeVisible(['category','credit_card']);
        });
        $bill->makeVisible(['bill_parent']);
        return $bill;
    }
}
