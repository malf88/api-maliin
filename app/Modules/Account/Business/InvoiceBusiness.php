<?php

namespace App\Modules\Account\Business;


use App\Models\CreditCard;
use App\Models\Invoice;
use App\Modules\Account\DTO\InvoiceDTO;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use App\Modules\Account\Impl\Business\BillStandarizedInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InvoiceBusiness implements InvoiceBusinessInterface
{

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

    public function createInvoiceForCreditCardByDate(CreditCard $creditCard, Carbon $date):InvoiceDTO
    {
        $invoice = $this->getInvoiceByCreditCardAndDate($creditCard->id,$date);
        if($invoice) {
            return $invoice;
        }

        return $this->invoiceRepository->insertInvoice(
            new InvoiceDTO($this->getInvoiceData($creditCard,$date))
        );
    }

    private function getInvoiceData(CreditCard $creditCard, Carbon $date):array
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
        $startDate = clone $date;
        $days = 30;
        if($date->day <= $closeDay){
            $startDate->subDays($days);
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
        $endDate = clone $date;
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
        if($closeDay > $days){
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
            $dueDate->setDay(2);
            $dueDate->addDays($days);
            if($dueDay > $days) {
                $dueDate->setDay($days);
            }else{
                $dueDate->setDay($dueDay);
            }

        }else{
            if($dueDay > $days) {
                $dueDate->setDay($days);
            }else{
                $dueDate->setDay($dueDay);
            }
        }
        return $dueDate;
    }

    private function generateMonthReference(Carbon $dueDate):int
    {
        return $dueDate->month;
    }

    public function payInvoice(int $invoiceId):Model
    {
        $invoiceWithBills = $this
                    ->invoiceRepository
                    ->getInvoiceWithBills($invoiceId);

        $invoiceWithBills
            ->bills
            ->each(function($item,$key){
                unset($item->bill_parent);
                $item->pay_day = Carbon::now();
                $item->save();
                $item->refresh();
            });
        $invoice = $this
                    ->invoiceRepository
                    ->getInvoice($invoiceId);
        $invoice->pay_day = Carbon::now();
        $invoice->save();

        return $invoiceWithBills->refresh();
    }

    public function getInvoicesWithBill(int $creditCardId):Collection
    {
        return $this
                ->invoiceRepository
                ->getInvoicesWithBills($creditCardId);
    }

    public function getInvoiceWithBills(int $invoiceId, bool $normalize = false):Model
    {
        $invoice =  $this
            ->invoiceRepository
            ->getInvoiceWithBills($invoiceId);
        if($normalize){
            $invoice->bills = $this->billStandarized->normalizeListBills($invoice->bills);
        }else{
            $invoice->makeVisible('bills');
        }

        return $invoice;
    }

    public function getInvoiceWithBillsInPDF(BillPdfInterface $billPdfService,int $invoiceId, bool $normalize = false):void
    {
        $invoice =  $this
            ->getInvoiceWithBills($invoiceId,$normalize);
        $pdf = $billPdfService->generate(Collection::make($invoice->toArray()));

        $pdf->stream($invoiceId.'-'.$invoice->start_date.'-'.$invoice->end_date.'.pdf');
    }

}
