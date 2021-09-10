<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\ServicesLocal\BillServiceLocal;

class BillController extends Controller
{
    private BillServiceLocal $BillServices;
    public function __construct(BillServiceLocal $BillServices)
    {
        $this->BillServices = $BillServices;
    }
}
