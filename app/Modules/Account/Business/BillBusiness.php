<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\Business\BillStandarizedInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Services\BillStandarizedService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class BillBusiness implements BillBusinessInterface
{

    public function __construct(
        private BillRepositoryInterface $billRepository,
        private CreditCardBusinessInterface $creditCardBusiness,
        private BillStandarizedService $billStandarized
    )
    {

    }


    public function getBillsByAccount(int $accountId, bool $normalize = false):Collection
    {
        if(Auth::user()->userHasAccount($accountId)){
            if($normalize){
                return $this->billStandarized->normalizeListBills($this->billRepository->getBillsByAccount($accountId));
            }else{
                return $this->billRepository->getBillsByAccount($accountId);
            }

        }else{
            throw new ItemNotFoundException('Conta não encontrada');
        }
    }

    public function getBillsByAccountBetween(int $accountId,array $rangeDate, bool $normalize = false):Collection
    {

        if(Auth::user()->userHasAccount($accountId)){
            if($normalize) {
                $billList = $this->billStandarized->normalizeListBills($this->billRepository->getBillsByAccountWithRangeDate(
                    accountId: $accountId,
                    rangeDate: $rangeDate
                ));
            }else{
                $billList = $this->billRepository->getBillsByAccountWithRangeDate(
                    accountId: $accountId,
                    rangeDate: $rangeDate
                );
            }
            return Collection::make([
                'bills' => $billList,
                'total' => Collection::make([
                    'total_cash_in' => $this->billRepository->getTotalCashIn($billList),
                    'total_cash_out' => $this->billRepository->getTotalCashOut($billList),
                    'total_estimated' =>$this->billRepository->getTotalEstimated($billList),
                    'total_paid' =>$this->billRepository->getTotalPaid($billList)
                ])
            ]);
        }else{
            throw new ItemNotFoundException('Conta não encontrada');
        }
    }

    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->billStandarized->normalizeListBills($this->billRepository->getBillsByAccount($accountId,true));
        }else{
            throw new ItemNotFoundException('Conta não encontrada');
        }
    }

    public function insertBill(int $accountId,$billData):Model|Collection
    {
        if(Auth::user()->userHasAccount($accountId)){
            if(isset($billData['portion']) && $billData['portion'] > 1){
                return $this->saveMultiplePortions($accountId,$billData);
            }else{
                $this->processCreditCardBill($billData);
                return $this->billRepository->saveBill($accountId,$billData);
            }
        }else{
            throw new ItemNotFoundException('Conta não encontrada.');
        }
    }
    private function processCreditCardBill(array $billData):void
    {
        if(!isset($billData['credit_card_id']) ||  $billData['credit_card_id'] == null)
            return;
        $this->creditCardBusiness->generateInvoiceByBill($billData['credit_card_id'],$billData['date']);
    }

    private function getDueDate(array $billData):Carbon|null
    {
        return (isset($billData['due_date']) && $billData['due_date'] != null)? Carbon::create($billData['due_date']) : null;
    }

    private function saveMultiplePortions(int $accountId, array $billData):Collection
    {
        if(isset($billData['credit_card_id']))
            $billData['due_date'] = null;
        $billsInserted = new Collection();
        $totalPortion = $billData['portion'];
        $descriptionBeforeCreateBill = $billData['description'];
        $billData['description'] = $this->getNewDescriptionWithPortion(
            $descriptionBeforeCreateBill,
            1,
            $totalPortion
        );
        $due_date = $this->getDueDate($billData);
        $billData['portion'] = 1;
        $billData['due_date'] = $due_date;
        $this->processCreditCardBill($billData);
        $billParent = $this->billRepository->saveBill($accountId,$billData);
        $billsInserted->add($billParent);
        $date = Carbon::createFromFormat('Y-m-d',$billData['date']);
        $due_date = $this->addMonthDueDate($due_date);
        $date->addMonth();
        for($interatorPortion = 2; $interatorPortion <= $totalPortion; $interatorPortion++){

            $billData['description'] = $this->getNewDescriptionWithPortion(
                $descriptionBeforeCreateBill,
                $interatorPortion,
                $totalPortion
            );
            $billData['bill_parent_id'] = $billParent->id;
            $billData['portion'] = $interatorPortion;
            $billData['due_date'] = $due_date;
            $billData['date'] = $date;

            $this->processCreditCardBill($billData);
            $bill = $this->billRepository->saveBill($accountId,$billData);
            $billsInserted->add($bill);
            $due_date = $this->addMonthDueDate($due_date);
            $date->addMonth();
        }
        return $billsInserted;
    }
    public function getBillById(int $billId):Model
    {
        $bill = $this->billStandarized->normalizeBill($this->billRepository->getBillById($billId));

        if(Auth::user()->userHasAccount($bill->account_id)){
            return $bill;
        }else{
            throw new ItemNotFoundException('Conta a pagar não encontrada');
        }
    }

    public function updateBill(int $billId,array $billData):Model|Collection
    {
        $this->getBillById($billId);
        if(!isset($billData['update_childs']) || !$billData['update_childs']){
            $this->processCreditCardBill($billData);
            return $this->billRepository->updateBill($billId,$billData);
        }else{
            return $this->updateChildBill($billId,$billData);
        }
    }

    private function updateChildBill(int $billId,array $billData):Model
    {
        $bill = $this->getBillById($billId);
        $due_date = $this->getDueDate($billData);
        $totalBillsSelected = $bill->bill_parent->count()+1;
        $description = $billData['description'];
        $date = Carbon::make($billData['date']);
        $billData['due_date'] = $due_date? $due_date->format('Y-m-d'): null;
        $billData['description'] = $this->getNewDescriptionWithPortion($description,$bill->portion,$totalBillsSelected);
        $this->billRepository->updateBill($billId, $billData);
        $this->processCreditCardBill($billData);
        $due_date = $this->addMonthDueDate($due_date);
        $date->addMonth();
        $bill->bill_parent->each(function($item,$key) use($date,$due_date, $totalBillsSelected, $description, $billData)
        {
            if($item->pay_day == null) {
                $billData['description'] = $this->getNewDescriptionWithPortion(
                                                $description,
                                                $item->portion,
                                                $totalBillsSelected);
                $billData['date'] = $date;
                $billData['due_date'] = $due_date??null;
                $this->billRepository->updateBill($item->id, $billData);
                $this->processCreditCardBill($billData);
                $due_date = $this->addMonthDueDate($due_date);
                $date->addMonth();
            }
            $item->refresh();
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

    public function getPeriodWithBill(int $accountId):Collection
    {
        if(Auth::user()->userHasAccount($accountId)) {
            return $this->billRepository->getMonthWithBill($accountId);
        }else{
            throw new ItemNotFoundException('Conta a pagar não encontrada');
        }

    }
}
