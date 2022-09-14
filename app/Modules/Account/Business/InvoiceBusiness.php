<?php

namespace App\Modules\Account\Business;


use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\DTO\InvoiceDTO;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use App\Modules\Account\Impl\Business\BillStandarizedInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Traits\RepositoryTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InvoiceBusiness implements InvoiceBusinessInterface
{
    use RepositoryTrait;
    const START_DAY_IN_MONTH = 2;
    const DAYS_IN_MONTH_DEFAULT = 30;

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private BillStandarizedInterface  $billStandarized
    )
    {

    }
    public function getInvoiceByCreditCardAndDate(int $creditCardId,Carbon $date):InvoiceDTO|null
    {
        return $this->invoiceRepository->getInvoiceByCreditCardAndDate($creditCardId,$date);
    }

    public function createInvoiceForCreditCardByDate(CreditCardDTO $creditCard, Carbon $date):InvoiceDTO
    {
        $invoice = $this->getInvoiceByCreditCardAndDate($creditCard->id,$date);
        if($invoice) {
            return $invoice;
        }
        return $this->invoiceRepository->insertInvoice(
            new InvoiceDTO($this->getInvoiceData($creditCard,$date))
        );
    }

    private function getInvoiceData(CreditCardDTO $creditCard, Carbon $date):array
    {

        $startDate  = $this->generateStartDate($date,$creditCard->close_day);
        $endDate    = $this->generateEndDate($date,$creditCard->close_day);
        $dueDate    = $this->generateDueDate($endDate,$creditCard->due_day);
       return [
            'start_date'        =>  $startDate,
            'end_date'          =>  $endDate,
            'due_date'          =>  $dueDate,
            'month_reference'   =>  $this->generateMonthReference($dueDate),
            'credit_card_id'    =>  $creditCard->id
        ];
    }
    private function generateStartDate(Carbon $date,int $closeDay):Carbon
    {
        $startDate = Carbon::make($date);

        if($date->day <= $closeDay){
            $startDate->subDays(self::DAYS_IN_MONTH_DEFAULT);
            $startDate = $this->setDaysInCloseAndStartDate($startDate,$closeDay);
            $startDate->addDay();
        }else{
            $startDate = $this->setDaysInCloseAndStartDate($startDate,$closeDay);
            $startDate->addDay();
        }
        return $startDate;
    }

    private function generateEndDate(Carbon $date,int $closeDay):Carbon
    {
        $endDate = Carbon::make($date);
        $days = $date->daysInMonth;
        if($date->day <= $closeDay){
            $endDate = $this->setDaysInCloseAndStartDate($endDate,$closeDay);
        }else{
            $endDate = $this->setDaysInCloseAndStartDate($endDate,$closeDay);
            $endDate->addDays($days);
        }
        return $endDate;
    }
    private function setDaysInCloseAndStartDate(Carbon $date,int $closeDay):Carbon
    {
        $days = $date->daysInMonth;
        if($this->dayGreaterThanNumberOfDaysInTheMonth($closeDay,$days)){
            $date->setDay($days);
        }else{
            $date->setDay($closeDay);
        }
        return $date;
    }
    private function generateDueDate(Carbon $endDate, int $dueDay):Carbon
    {
        $dueDate = clone $endDate;

        $days = $dueDate->daysInMonth;
        if($dueDay <= $endDate->day){
            $dueDate->setDay(self::START_DAY_IN_MONTH);
            $dueDate->addDays($days);
        }
        if($this->dayGreaterThanNumberOfDaysInTheMonth($dueDay,$days)) {
            $dueDate->setDay($days);
        }else{
            $dueDate->setDay($dueDay);
        }
        return $dueDate;
    }
    private function dayGreaterThanNumberOfDaysInTheMonth($day1,$daysInMonth):bool
    {
        return $day1 > $daysInMonth;
    }

    private function generateMonthReference(Carbon $dueDate):int
    {
        return $dueDate->month;
    }

    public function payInvoice(int $invoiceId):InvoiceDTO
    {
        try{
            $this->startTransaction();
            $invoiceWithBills = $this
                        ->invoiceRepository
                        ->getInvoiceWithBills($invoiceId);

            $invoiceWithBills = $this->invoiceRepository->payBillForInvoice($invoiceWithBills);
            $invoice = $this
                        ->invoiceRepository
                        ->getInvoice($invoiceId);
            $invoice->pay_day = Carbon::now();
            $invoice = $this->invoiceRepository->saveInvoice($invoice);
            $this->commitTransaction();
            return $invoice;

        }catch (\Exception $e){
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function getInvoicesWithBill(int $creditCardId):Collection
    {
        return $this
                ->invoiceRepository
                ->getInvoicesWithBills($creditCardId);
    }
    public function getInvoiceWithBillsNormalized(int $invoiceId):InvoiceDTO
    {
        $invoice =  $this
            ->invoiceRepository
            ->getInvoiceWithBills($invoiceId);
        $invoice->bills = $this->billStandarized->normalizeListBills($invoice->bills);

        return new InvoiceDTO($invoice->toArray());
    }

    public function getInvoiceWithBills(int $invoiceId):InvoiceDTO
    {
        $invoice =  $this
            ->invoiceRepository
            ->getInvoiceWithBills($invoiceId);

        //$invoice->makeVisible('bills');
        $invoiceDTO = new InvoiceDTO(...$invoice->toArray());
        //$invoiceDTO->credit_card = $invoice->credit_card;
        return $invoiceDTO;
    }

    public function getInvoiceWithBillsInPDF(BillPdfInterface $billPdfService,int $invoiceId):void
    {
        $invoice =  $this
            ->getInvoiceWithBills($invoiceId);
        $pdf = $billPdfService->generate(Collection::make($invoice->toArray()));

        $pdf->stream($invoiceId.'-'.$invoice->start_date.'-'.$invoice->end_date.'.pdf');
    }

}
