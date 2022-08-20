<?php

namespace App\Modules\Account\Business;

use App\Abstracts\DTOAbstract;
use App\Modules\Account\DTO\BillDTO;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Services\BillStandarizedService;
use App\Traits\RepositoryTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BillBusiness implements BillBusinessInterface
{
    use RepositoryTrait;
    public function __construct(
        private BillRepositoryInterface $billRepository,
        private CreditCardBusinessInterface $creditCardBusiness,
        private BillStandarizedService $billStandarized
    )
    {

    }

    public function getBillsByAccountNormalized(int $accountId):LengthAwarePaginator
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->billStandarized->normalizeListBills($this->billRepository->getBillsByAccount($accountId));
        }else{
            throw new NotFoundHttpException('Conta não encontrada');
        }
    }

    public function getBillsByAccount(int $accountId):Collection
    {
        if(Auth::user()->userHasAccount($accountId)){
            return $this->billRepository->getBillsByAccount($accountId);
        }else{
            throw new NotFoundHttpException('Conta não encontrada');
        }
    }

    public function getBillListNormalized(int $accountId,array $rangeDate):Collection
    {
        return $this->billStandarized->normalizeListBills(
            $this->billRepository->getBillsByAccountWithRangeDate(
                accountId: $accountId,
                rangeDate: $rangeDate
            )
        );
    }

    public function getBillList(int $accountId,array $rangeDate):Collection
    {

        return $this->billRepository->getBillsByAccountWithRangeDate(
            accountId: $accountId,
            rangeDate: $rangeDate
        );

    }
    public function getBillsByAccountBetween(int $accountId,array $rangeDate):Collection
    {
        if(!Auth::user()->userHasAccount($accountId))
            throw new NotFoundHttpException('Conta não encontrada');

        $billList = $this->getBillList($accountId,$rangeDate);
        return Collection::make([
            'bills' => $billList,
            'total' => Collection::make([
                'total_cash_in' => $this->billRepository->getTotalCashIn($billList),
                'total_cash_out' => $this->billRepository->getTotalCashOut($billList),
                'total_estimated' =>$this->billRepository->getTotalEstimated($billList),
                'total_paid' =>$this->billRepository->getTotalPaid($billList)
            ])
        ]);

    }

    public function getBillsByAccountPaginate(int $accountId):LengthAwarePaginator
    {
        if(!Auth::user()->userHasAccount($accountId))
            throw new NotFoundHttpException('Conta não encontrada');

        return $this->billStandarized->normalizeListBills($this->billRepository->getBillsByAccountPaginate($accountId));
    }

    public function insertBill(int $accountId, BillDTO $billData):DTOAbstract|Collection
    {
        if(!Auth::user()->userIsOwnerAccount($accountId))
            throw new NotFoundHttpException('Conta não encontrada.');

        if($billData->portion > 1){
            return $this->saveMultiplePortions($accountId,$billData);
        }else{
            $this->processCreditCardBill($billData);
            return $this->billRepository->saveBill($accountId,$billData);
        }

    }

    private function processCreditCardBill(BillDTO $billData):void
    {
        if($billData->credit_card_id == null)
            return;
        $this->creditCardBusiness->generateInvoiceByBill($billData->credit_card_id, $billData->date);
    }

    private function getDueDate(string|null $date):Carbon|null
    {
        return ($date != null)? Carbon::create($date) : null ;
    }

    private function saveMultiplePortions(int $accountId, BillDTO $billData):Collection
    {
        if(isset($billData->credit_card_id))
            $billData->due_date = null;

        try {
            $this->startTransaction();
            $billsInserted = new Collection();
            $totalPortion = $billData->portion;
            $descriptionBeforeCreateBill = $billData->description;
            $billData->description = $this->getNewDescriptionWithPortion(
                $descriptionBeforeCreateBill,
                1,
                $totalPortion
            );
            $due_date = $this->getDueDate($billData->due_date);

            $billData->portion = 1;
            $billData->due_date = $due_date;
            $this->processCreditCardBill($billData);
            $billParent = $this->billRepository->saveBill($accountId, $billData);
            $billsInserted->add($billParent);
            $date = Carbon::create($billData->date);

            $dayOfMonthDueDate = $due_date ? $due_date->day : null;
            $dayOfMonthDate = $date->day;
            $due_date = $this->addMonth($due_date, $dayOfMonthDueDate);
            $date = (!$due_date)? $this->addMonth($date, $dayOfMonthDate): $date;

            for ($interatorPortion = 2; $interatorPortion <= $totalPortion; $interatorPortion++) {
                $billData->description = $this->getNewDescriptionWithPortion(
                    $descriptionBeforeCreateBill,
                    $interatorPortion,
                    $totalPortion
                );
                $billData->bill_parent_id = $billParent->id;
                $billData->portion = $interatorPortion;
                $billData->due_date = $due_date;

                $billData->date = $date;
                $this->processCreditCardBill($billData);
                $bill = $this->billRepository->saveBill($accountId, $billData);
                $billsInserted->add($bill);
                $due_date = $this->addMonth($due_date, $dayOfMonthDueDate);
                $date = (!$due_date)? $this->addMonth($date, $dayOfMonthDate): $date;

            }
            $this->commitTransaction();

            return $billsInserted;
        }catch (ValidationException $exception){
            $this->rollbackTransaction();
            throw $exception;
        }


    }
    public function getBillById(int $billId):Model
    {
        $bill = $this->billStandarized->normalizeBill($this->billRepository->getBillById($billId));

        if(Auth::user()->userHasAccount($bill->account_id)){
            return $bill;
        }else{
            throw new NotFoundHttpException('Conta a pagar não encontrada');
        }
    }

    public function updateBill(int $billId,BillDTO $billData):DTOAbstract|Collection
    {
        $bill = $this->getBillById($billId);
        if(!Auth::user()->userIsOwnerAccount($bill->account_id))
            throw new NotFoundHttpException('Lançamento não encontrado');

        if (!$billData->update_childs) {
            $this->processCreditCardBill($billData);
            return $this->billRepository->updateBill($billId, $billData);
        } else {
            return $this->updateChildBill($billId, $billData);
        }

    }
    private function updateChildBill(int $billId,BillDTO $billData):DTOAbstract
    {
        try {
            $this->startTransaction();
            $bill = $this->getBillById($billId);
            $due_date = $this->getDueDate($billData->due_date);
            $totalBillsSelected = $bill->bill_parent->count() + 1;
            $description = $billData->description;
            $date = Carbon::make($billData->date);
            $dayOfMonthDueDate = $due_date ? $due_date->day : null;
            $dayOfMonthDate = $date->day;
            $billData->due_date = $due_date ? $due_date->format('Y-m-d') : null;
            $billData->description = $this->getNewDescriptionWithPortion($description, $bill->portion, $totalBillsSelected);
            $this->billRepository->updateBill($billId, $billData);
            $this->processCreditCardBill($billData);
            $due_date = $this->addMonth($due_date, $dayOfMonthDueDate);
            $date = (!$due_date)? $this->addMonth($date, $dayOfMonthDate): $date;
            $bill->bill_parent->each(function ($item, $key)
                    use ($date, $due_date, $totalBillsSelected, $description, $billData, $bill, $dayOfMonthDueDate, $dayOfMonthDate) {
                if ($item->pay_day == null && $item->portion > $bill->portion) {
                    $billData->description = $this->getNewDescriptionWithPortion(
                        $description,
                        $item->portion,
                        $totalBillsSelected);
                    $billData->date = $date;
                    $billData->due_date = $due_date ?? null;
                    $this->billRepository->updateBill($item->id, $billData);
                    $this->processCreditCardBill($billData);
                    $due_date = $this->addMonth($due_date, $dayOfMonthDueDate);
                    $date = (!$due_date)? $this->addMonth($date, $dayOfMonthDate): $date;
                }
                $item->refresh();
            });
            $this->commitTransaction();
            return new BillDTO($bill->refresh()->toArray());
        } catch (ValidationException $exception) {
            $this->rollbackTransaction();
            throw $exception;
        }
    }
    private function getNewDescriptionWithPortion(string $description,int $portionActual,int $portionTotal):string
    {
        $description = preg_replace('/\s\[\d{1,}\/\d{1,}\]/','', $description);
        return $description . ' ['.$portionActual. '/' .$portionTotal .']';
    }

    private function addMonth(Carbon|null $date, int $dayOfMonth = null):Carbon|null
    {
        $newDate = $date ? Carbon::make($date) : null;
        if($newDate != null){
            $newDate->addMonthNoOverflow();
            if($dayOfMonth){
                $newDate->setUnitNoOverflow('day',$dayOfMonth,'month');
            }
        }else{
            return null;
        }
        return $date;
    }
    public function deleteBill(int $billId):bool
    {
        $bill = $this->getBillById($billId);
        if(!Auth::user()->userIsOwnerAccount($bill->account_id))
            throw new NotFoundHttpException('Lançamento não encontrado');

        return $this->billRepository->deleteBill($billId);

    }

    public function getPeriodWithBill(int $accountId):Collection
    {
        if(!Auth::user()->userHasAccount($accountId))
            throw new NotFoundHttpException('Conta a pagar não encontrada');

        return $this->billRepository->getMonthWithBill($accountId);


    }
    public function generatePdfByPeriod(BillPdfInterface $billPdfService, int $accountId,array $period):void
    {
        $bills = $this->getBillsByAccountBetween($accountId,$period);
        $domPdf = $billPdfService->generate($bills);
        $domPdf->stream($accountId.'-'.$period[0].'-'.$period[1].'.pdf');
    }
}
