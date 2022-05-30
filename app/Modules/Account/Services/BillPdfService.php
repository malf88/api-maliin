<?php

namespace App\Modules\Account\Services;

use App\Models\Bill;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;

class BillPdfService implements BillPdfInterface
{
    public function mountHtmlWithBill(Collection $billList)
    {
        return view('pdf.billList', ['billList' => $billList])->render();
    }
    public function generate(Collection $billList):Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Courier');

        $dompdf = new Dompdf($options);
        //$dompdf->loadHtml('hello world');
        $dompdf->loadHtml($this->mountHtmlWithBill($billList));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf;
    }
}
