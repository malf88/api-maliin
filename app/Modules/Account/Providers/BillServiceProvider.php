<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Controllers\BillController;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Impl\Business\BillPdfInterface;
use App\Modules\Account\Impl\Business\BillStandarizedInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Services\BillPdfService;
use App\Modules\Account\Services\BillService;
use App\Modules\Account\Services\BillStandarizedService;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Illuminate\Support\ServiceProvider;

class BillServiceProvider extends ServiceProvider
{

    public $bindings = [
        BillPdfInterface::class => BillPdfService::class,
        BillBusinessInterface::class => BillBusiness::class,
        BillRepositoryInterface::class => BillRepository::class,
        BillStandarizedInterface::class => BillStandarizedService::class,

    ];

}
