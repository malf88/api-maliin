<?php

namespace App\Modules\Account\Business;


use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InvoiceBusiness implements InvoiceBusinessInterface
{

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private CreditCardBusinessInterface $creditCardBusiness)
    {

    }
    public function getInvoiceByCreditCardAndDate(int $creditCardId,Carbon $date):Model|null
    {
        $this->creditCardBusiness->getCreditCardById($creditCardId);
        return $this->invoiceRepository->getInvoiceByCreditCardAndDate($creditCardId,$date);
    }

    public function createInvoiceForCreditCardByDate(int $creditCardId, Carbon $date):Model
    {
        $invoice = $this->getInvoiceByCreditCardAndDate($creditCardId,$date);
        if($invoice) {
            return $invoice;
        }
        return $this->invoiceRepository->insertInvoice(
            $this->getInvoiceData($creditCardId,$date)
        );
    }

    private function getInvoiceData($creditCardid, Carbon $date):array
    {
        $creditCard = $this->creditCardBusiness->getCreditCardById($creditCardid);
        $startDate  = $this->generateStartDate($date,$creditCard->close_day);
        $endDate    = $this->generateEndDate($date,$creditCard->close_day);
        $dueDate    = $this->generateDueDate($endDate,$creditCard->due_day);

       return [
            'start_date'        =>  $startDate,
            'end_date'          =>  $endDate,
            'due_date'          =>  $dueDate,
            'month_reference'   =>  $this->generateMonthReference($dueDate),
            'credit_card_id'    =>  $creditCardid
        ];
    }
    private function generateStartDate(Carbon $date,int $closeDay):Carbon
    {
        $startDate = clone $date;

        if($date->day <= $closeDay){
            $startDate->subDays(30);
            $startDate->setDay($closeDay);
            $startDate->addDay();
        }else{
            $startDate->setDay($closeDay);
            $startDate->addDay();
        }
        return $startDate;
    }
    private function generateEndDate(Carbon $date,int $closeDay):Carbon
    {
        $endDate = clone $date;

        if($date->day <= $closeDay){
            $endDate->setDay($closeDay);
        }else{
            $endDate->setDay($closeDay);
            $endDate->addMonth();
        }
        return $endDate;
    }
    private function generateDueDate(Carbon $endDate, int $dueDay):Carbon
    {
        $dueDate = clone $endDate;
        if($dueDay <= $endDate->day){
            $dueDate->addDays(30);
            $dueDate->setDay($dueDay);
        }else{
            $dueDate->setDay($dueDay);
        }
        return $dueDate;
    }

    private function generateMonthReference(Carbon $dueDate):int
    {
        return $dueDate->month;
    }


}
