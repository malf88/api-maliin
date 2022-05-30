<?php

namespace Tests\Unit\Account\Service;

use App\Models\Bill;
use App\Modules\Account\Services\BillPdfService;
use App\Modules\Account\Services\InvoicePdfService;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Unit\Account\Factory\DataFactory;

class InvoicePdfServiceTest extends TestCase
{
    /**
     * @test
     */
    public function deveGerarPdf(){
        $dataFactory = new DataFactory();

        $billPdfService = new InvoicePdfService();
        $invoice = $dataFactory->factoryInvoiceList();
        $pdf = $billPdfService->generate(Collection::make($invoice->get(0)->toArray()));
        $this->assertIsObject($pdf);
    }
}
