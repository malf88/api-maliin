<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;

class InvoiceController extends Controller
{
    private InvoiceServiceLocal $InvoiceServices;
    public function __construct(InvoiceServiceLocal $InvoiceServices)
    {
        $this->InvoiceServices = $InvoiceServices;
    }
}
