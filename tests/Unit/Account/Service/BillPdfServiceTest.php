<?php

namespace Tests\Unit\Account\Service;

use App\Models\Bill;
use App\Modules\Account\Services\BillPdfService;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Unit\Account\Factory\DataFactory;

class BillPdfServiceTest extends TestCase
{
    /**
     * @test
     */
    public function deveGerarPdf(){
        $dataFactory = new DataFactory();

        $billPdfService = new BillPdfService();
        $billList = Collection::make($dataFactory->factoryBills());
        $pdf = $billPdfService->generate($billList);
        $this->assertIsObject($pdf);
    }
}
