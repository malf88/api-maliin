<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InvoiceService implements InvoiceServiceLocal
{

    public function __construct(private InvoiceBusinessInterface $invoiceBusiness)
    {

    }

    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Model|null
    {
        // TODO: Implement getInvoiceByCreditCardAndDate() method.
    }

    public function createInvoiceForCreditCardByDate(Model $creditCard, Carbon $date): Model
    {
        // TODO: Implement createInvoiceForCreditCardByDate() method.
    }

    public function payInvoiceAndBill(int $invoiceId): Model
    {
        return $this->invoiceBusiness->payInvoice($invoiceId);
    }
    public function getInvoiceWithBills(int $invoiceId):Model
    {
        return $this->invoiceBusiness->getInvoiceWithBills($invoiceId);
    }

    public function getInvoiceWithBillsInPDF(int $invoiceId, bool $normalize = false): void
    {
        $this->invoiceBusiness->getInvoiceWithBillsInPDF(new InvoicePdfService(),$invoiceId,$normalize);
    }
}
