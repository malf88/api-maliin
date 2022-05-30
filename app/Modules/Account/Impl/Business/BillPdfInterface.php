<?php

namespace App\Modules\Account\Impl\Business;

use Dompdf\Dompdf;
use Illuminate\Support\Collection;

interface BillPdfInterface
{
    public function generate(Collection $billList):Dompdf;
}
