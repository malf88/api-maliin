<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Controllers\InvoiceController;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\BillStandarizedInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Repository\InvoiceRepository;
use App\Modules\Account\Services\BillStandarizedService;
use App\Modules\Account\Services\InvoiceService;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public $bindings = [
        InvoiceBusinessInterface::class => InvoiceBusiness::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
    ];
}
