<?php

namespace App\Modules\Account\Business;

use App\Abstracts\DTOAbstract;
use App\Exceptions\InvalidValueException;
use App\Helpers\BillHelper;
use App\Models\Bill;
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
use Illuminate\Support\Collection as BaseCollection;
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

        if($billData->credit_card_id != null && !$this->creditCardBusiness->isCreditCardValid($billData->credit_card_id))
            throw new InvalidValueException('Cartão de crédito não é válido');

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

    private function recreateDTO(BillDTO $billData):BillDTO
    {
        return new BillDTO($billData->toArray());
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
            $creditCardId = $billData->credit_card_id;
            $categoryId = $billData->category_id;
            $due_date = $this->getDueDate($billData->due_date);


            $billData = $this->loadDataForSave($billData,[
                'due_date' => $due_date,
                'date' => $billData->date,
                'credit_card_id' => $creditCardId,
                'description' => $descriptionBeforeCreateBill,
                'portion' => 1,
                'category_id' => $categoryId,
                'totalBillsSelected' => $totalPortion
            ]);

            $this->processCreditCardBill($billData);
            $billParent = $this->billRepository->saveBill($accountId, $billData);
            $billsInserted->add($billParent);
            $billData = $this->recreateDTO($billData);
            $date = Carbon::create($billData->date);

            $dayOfMonthDueDate = $due_date ? $due_date->day : null;
            $dayOfMonthDate = $date->day;
            $due_date = BillHelper::addMonth($due_date, $dayOfMonthDueDate);
            $date = (!$due_date)? BillHelper::addMonth($date, $dayOfMonthDate): $date;

            for ($interatorPortion = 2; $interatorPortion <= $totalPortion; $interatorPortion++) {
                $billData = $this->recreateDTO($billData);

                $billData->bill_parent_id = $billParent->id;
                $billData = $this->loadDataForSave($billData,[
                    'due_date' => $due_date,
                    'date' => $date,
                    'credit_card_id' => $creditCardId,
                    'description' => $descriptionBeforeCreateBill,
                    'portion' => $interatorPortion,
                    'category_id' => $categoryId,
                    'totalBillsSelected' => $totalPortion
                ]);

                $this->processCreditCardBill($billData);
                $bill = $this->billRepository->saveBill($accountId, $billData);
                $billsInserted->add($bill);
                $due_date = BillHelper::addMonth($due_date, $dayOfMonthDueDate);
                $date = (!$due_date)? BillHelper::addMonth($date, $dayOfMonthDate): $date;

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

    public function payBill(int $billId,BillDTO $billData):DTOAbstract
    {
        $bill = $this->getBillById($billId);
        if(!Auth::user()->userIsOwnerAccount($bill->account_id))
            throw new NotFoundHttpException('Lançamento não encontrado');

        return $this->billRepository->updatePayDayBill($billId, $billData);

    }

    public function updateBill(int $billId,BillDTO $billData):DTOAbstract|BaseCollection
    {
        $bill = $this->getBillById($billId);
        if(!Auth::user()->userIsOwnerAccount($bill->account_id))
            throw new NotFoundHttpException('Lançamento não encontrado');

        if($billData->credit_card_id != null && !$this->creditCardBusiness->isCreditCardValid($billData->credit_card_id))
            throw new InvalidValueException('Cartão de crédito não é válido');

        if (!$billData->update_childs) {
            $this->processCreditCardBill($billData);
            return $this->billRepository->updateBill($billId, $billData);
        } else {
            return $this->updateChildBill($billId, $billData);
        }

    }
    private function loadDataForSave(BillDTO $billData,array $dados):BillDTO
    {
        $billData->due_date = $dados['due_date'] ?: null;
        $billData->date =  Carbon::make($dados['date']);
        $billData->credit_card_id = $dados['credit_card_id'];
        $billData->description = $this->getNewDescriptionWithPortion($dados['description'], $dados['portion'], $dados['totalBillsSelected']);
        $billData->category_id = $dados['category_id'];
        return $billData;

    }
    private function updateChildBill(int $billId,BillDTO $billData):BaseCollection
    {
        $updatedBills = new BaseCollection();
        try {
            $this->startTransaction();
            $bill = $this->getBillById($billId);
            $due_date = $this->getDueDate($billData->due_date);
            $totalBillsSelected = $bill->bill_parent->count() + 1;
            $description = $billData->description;
            $date = Carbon::make($billData->date);
            $categoryId = $billData->category_id;
            $creditCardId = $billData->credit_card_id;
            $dayOfMonthDueDate = $due_date ? $due_date->day : null;
            $dayOfMonthDate = $date->day;
            $billData = $this->loadDataForSave($billData,[
                'due_date' => $due_date,
                'date' => $date,
                'credit_card_id' => $creditCardId,
                'description' => $description,
                'portion' => $bill->portion,
                'category_id' => $categoryId,
                'totalBillsSelected' => $totalBillsSelected
            ]);
            $this->processCreditCardBill($billData);
            $updatedBill =  $this->billRepository->updateBill($billId, $billData);
            $updatedBills->add($updatedBill);
            $due_date = BillHelper::addMonth($due_date, $dayOfMonthDueDate);
            $date = (!$due_date)? BillHelper::addMonth($date, $dayOfMonthDate): $date;

            foreach($bill->bill_parent as $item){
                if ($item->pay_day == null && $item->portion > $bill->portion) {
                    $billData = new BillDTO($item->toArray());

                    $billData = $this->loadDataForSave($billData,[
                        'due_date' => $due_date,
                        'date' => $date,
                        'credit_card_id' => $creditCardId,
                        'description' => $description,
                        'portion' => $item->portion,
                        'category_id' => $categoryId,
                        'totalBillsSelected' => $totalBillsSelected
                    ]);

                    $updatedBill = $this->billRepository->updateBill($item->id, $billData);
                    $updatedBills->add($updatedBill);
                    $this->processCreditCardBill($billData);
                    $due_date = BillHelper::addMonth($due_date, $dayOfMonthDueDate);

                    $date = (!$due_date)? BillHelper::addMonth($date, $dayOfMonthDate): $date;

                }
            }
            $this->commitTransaction();
            return $updatedBills;
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

    public function getBillsByCreditCardId(int $creditCardId):Collection
    {
        return $this->billRepository->getBillsByCreditCardId($creditCardId);
    }

    public function generatePdfByPeriod(BillPdfInterface $billPdfService, int $accountId,array $period):void
    {
        $bills = $this->getBillsByAccountBetween($accountId,$period);
        $domPdf = $billPdfService->generate($bills);
        $domPdf->stream($accountId.'-'.$period[0].'-'.$period[1].'.pdf');
    }
}
