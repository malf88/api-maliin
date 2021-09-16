<?php

namespace App\Modules\Account\Impl\Business;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

interface InvoiceBusinessInterface
{
    public function getInvoiceByCreditCardAndDate(int $creditCardId, Carbon $date): Model|null;
    public function createInvoiceForCreditCardByDate(int $creditCardId, Carbon $date):Model;
}
