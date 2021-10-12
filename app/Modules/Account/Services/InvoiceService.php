<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Carbon\Carbon;
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
}
