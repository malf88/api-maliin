<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Impl\Bussines\InvoiceBusinessInterface;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InvoiceService implements InvoiceServiceLocal
{
    private InvoiceBusinessInterface $InvoiceBusiness;

    public function __construct(InvoiceBusinessInterface $InvoiceBusiness)
    {
        $this->InvoiceBusiness = $InvoiceBusiness;
    }
}
