<?php

namespace App\Modules\Account\Services;

use App\Models\Bill;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;

class InvoicePdfService implements BillPdfInterface
{
    public function mountHtmlWithBill(Collection $invoiceList)
    {
        return view('pdf.invoiceList', ['billList' => $invoiceList])->render();
    }
    public function generate(Collection $invoiceList):Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Courier');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->mountHtmlWithBill($invoiceList));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf;
    }
}
