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
        $bill->category = $this->billRepository->getCategory($bill);
        $bill->credit_card = $this->billRepository->getCreditCard($bill);
        $bill->makeVisible(['category','credit_card']);

        return $bill;
    }

    private function findChildBill(Model $bill):Model
    {
        if($bill->bill_parent_id != null){
            $bill->bill_parent = $this->billRepository->getChildBill($bill->id,$bill->bill_parent_id);
        }else{
            $bill->load('bill_parent');
        }
        $bill->bill_parent->each(function($item,$key){
            $item->category = $item->category;
            $item->credit_card = $item->credit_card;
            $item->makeVisible(['category','credit_card']);
        });
        $bill->makeVisible(['bill_parent']);
        return $bill;
    }
}
