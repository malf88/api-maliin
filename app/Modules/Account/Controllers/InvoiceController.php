<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceLocal $invoiceServices)
    {
    }
}
