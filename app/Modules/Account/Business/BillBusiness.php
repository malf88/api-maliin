<?php

namespace App\Modules\Account\Business;

use App\Models\Bill;
use App\Models\User;
use App\Modules\Account\Impl\BillRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
    private function findChildBill(Model $bill):Model
    {
        //dd($bill);
        if($bill->bill_parent_id != null){
            $bill->bill_parent = $this->billRepository->getChildBill($bill->id,$bill->bill_parent_id);
        }else{
            $bill->load('bill_parent');
        }
        $bill->makeVisible(['bill_parent']);

        return $bill;
    }
    public function normalizeListBills(Collection|LengthAwarePaginator $bills):Collection|LengthAwarePaginator
    {

        $bills->each(function($item,$key){
            $item = $this->findChildBill($item);
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

    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator
    {
        if($this->accountBusiness->userHasAccount(Auth::user(),$accountId)){
            return $this->normalizeListBills($this->billRepository->getBillsByAccount($accountId,true));
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
        $totalPortion = $billData['portion'];
        $descriptionBeforeCreateBill = $billData['description'];
        $billData['description'] = $descriptionBeforeCreateBill . ' [1/'.$billData['portion'].']';
        $billData['portion'] = 1;
        $billParent = $this->billRepository->saveBill($accountId,$billData);
        $billsInserted->add($billParent);
        $due_date = Carbon::create($billData['due_date']);
        $due_date->addMonth();
        for($interatorPortion = 2; $interatorPortion <= $totalPortion; $interatorPortion++){

            $billData['description'] = $descriptionBeforeCreateBill . '['.$interatorPortion.'/'.$totalPortion.']';
            $billData['bill_parent_id'] = $billParent->id;
            $billData['portion'] = $interatorPortion;
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
            return $this->findChildBill($bill);
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

    public function updateChildBill(int $billId,array $billData):Model
    {
        $bill = $this->getBillById($billId);
        $due_date = (isset($billData['due_date']) && $billData['due_date'] != null)? Carbon::create($billData['due_date']) : null;
        $totalBillsSelected = $bill->bill_parent->count()+1;
        $description = $billData['description'];
        $billData['due_date'] = $due_date? $due_date->format('Y-m-d'): null;
        $billData['description'] = $this->getNewDescriptionWithPortion($description,$bill->portion,$totalBillsSelected);
        $this->billRepository->updateBill($billId, $billData);
        $due_date = $this->addMonthDueDate($due_date);
        $bill->bill_parent->each(function($item,$key) use($due_date, $totalBillsSelected, $description, $billData)
        {
            if($item->pay_day == null) {
                $billData['description'] = $this->getNewDescriptionWithPortion(
                                                $description,
                                                $item->portion,
                                                $totalBillsSelected);
                $billData['due_date'] = $due_date? $due_date->format('Y-m-d'): null;
                $this->billRepository->updateBill($item->id, $billData);
                $due_date = $this->addMonthDueDate($due_date);
            }
            $item->refresh();
            //return $item;
        });
        return $bill->refresh();
    }
    private function getNewDescriptionWithPortion(string $description,int $portionActual,int $portionTotal):string
    {
        return $description . '['.$portionActual. '/' .$portionTotal .']';
    }

    private function addMonthDueDate(Carbon|null $date):Carbon|null
    {
        if($date != null){
            $date->addMonth();
        }else{
            return null;
        }
        return $date;
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
