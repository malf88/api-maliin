<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use App\Modules\Account\Bussines\BillBusiness;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BillService implements BillServiceLocal
{
    private BillBusiness $BillBusiness;

    public function __construct(BillBusiness $BillBusiness)
    {
        $this->BillBusiness = $BillBusiness;
    }
}
