<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;

class InvoiceService implements InvoiceServiceLocal
{

    public function __construct(private InvoiceBusinessInterface $invoiceBusiness)
    {

    }
}
