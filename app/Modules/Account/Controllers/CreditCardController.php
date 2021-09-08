<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;

class CreditCardController extends Controller
{
    private CreditCardServiceLocal $CreditCardServices;
    public function __construct(CreditCardServiceLocal $CreditCardServices)
    {
        $this->CreditCardServices = $CreditCardServices;
    }
}
