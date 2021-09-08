<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Bussines\CreditCardBusiness;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CreditCardService implements CreditCardServiceLocal
{
    private CreditCardBusiness $CreditCardBusiness;

    public function __construct(CreditCardBusiness $CreditCardBusiness)
    {
        $this->CreditCardBusiness = $CreditCardBusiness;
    }
}
