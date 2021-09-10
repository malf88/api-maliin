<?php

namespace App\Modules\Account\Business;

use App\Models\Bill;
use App\Models\User;
use App\Modules\Account\Impl\BillRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class BillBusiness
{
    private BillRepositoryInterface $billRepository;
    private AccountBusiness $accountBusiness;
    public function __construct(
        BillRepositoryInterface $billRepository,
        AccountBusiness $accountBusiness)
    {
        $this->billRepository = $billRepository;
        $this->accountBusiness = $accountBusiness;
    }
    public function normalizeListBills(Collection $bills):Collection
    {
        $bills->each(function($item,$key){
            $item->setVisible(['bill_parent']);
            $item->load('bill_parent');
        });
        return $bills;
    }
    public function getBillsByAccount(int $accountId):Collection
    {
        if($this->accountBusiness->userHasAccount(Auth::user(),$accountId)){
            return $this->normalizeListBills($this->billRepository->getBillsByAccount($accountId));
        }else{
            throw new ItemNotFoundException('Conta não encontrada');
        }
    }

    public function insertBill(int $accountId,$billData):Model|Collection
    {
        if($this->accountBusiness->userHasAccount(Auth::user(),$accountId)){
            if($billData['portion'] > 1){
                return $this->saveMultiplePortions($accountId,$billData);
            }else{
                return $this->billRepository->saveBill($accountId,$billData);
            }
        }else{
            throw new ItemNotFoundException('Conta não encontrada.');
        }
    }

    private function saveMultiplePortions(int $accountId, array $billData):Collection
    {
        $billsInserted = new Collection();
        $descriptionBeforeCreateBill = $billData['description'];
        $billData['description'] = $descriptionBeforeCreateBill . ' [1/'.$billData['portion'].']';
        $billParent = $this->billRepository->saveBill($accountId,$billData);
        $billsInserted->add($billParent);
        $due_date = Carbon::create($billData['due_date']);
        $due_date->addMonth();
        for($interatorPortion = 2; $interatorPortion <= $billData['portion']; $interatorPortion++){
            $billData['description'] = $descriptionBeforeCreateBill . '['.$interatorPortion.'/'.$billData['portion'].']';
            $billData['bill_parent_id'] = $billParent->id;
            $billData['due_date'] = $due_date->format('Y-m-d');
            $bill = $this->billRepository->saveBill($accountId,$billData);
            $billsInserted->add($bill);
            $due_date->addMonth();
        }
        return $billsInserted;
    }
    public function getBillById(int $billId):Model
    {
        $bill = $this->billRepository->getBillById($billId);
        if($this->userHasBill(Auth::user(),$bill)){
            return $bill;
        }else{
            throw new ItemNotFoundException('Conta a pagar não encontrada');
        }
    }

    public function updateBill(int $billId,array $billData):Model|Collection
    {
        $this->getBillById($billId);
        if(!isset($billData['update_childs']) || !$billData['update_childs']){
            return $this->billRepository->updateBill($billId,$billData);
        }else{
            return $this->updateChildBill($billId,$billData);
        }
    }

    public function updateChildBill(int $billId,array $billData):Collection
    {
        $bills = $this->billRepository->getChildBill($billId);
        $due_date = Carbon::create($billData['due_date']);
        $totalBillsSelected = $bills->count();
        $description = $billData['description'];
        $bills->each(function($item,$key) use($due_date, $totalBillsSelected, $description)
        {
            if($item->pay_day == null) {
                $newDescription = $description . '['.($key + 1) . '/' .$totalBillsSelected .']';
                $billData['description'] = $newDescription;
                $billData['due_date'] = $due_date->format('Y-m-d');
                $this->billRepository->updateBill($item->id, $billData);
                $due_date->addMonth();
            }
        });
        return $bills;
    }

    public function deleteBill(int $billId):bool
    {
        $this->getBillById($billId);
        return $this->billRepository->deleteBill($billId);
    }

    public function userHasBill(User $user, Model $bill)
    {
        return $bill->account->user->id == $user->id;
    }
}
