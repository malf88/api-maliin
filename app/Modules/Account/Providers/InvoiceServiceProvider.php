<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Controllers\InvoiceController;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Repository\InvoiceRepository;
use App\Modules\Account\Services\InvoiceService;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(InvoiceController::class)
            ->needs(InvoiceServiceLocal::class)
            ->give(InvoiceService::class);

        $this->app
            ->when(InvoiceBusiness::class)
            ->needs(InvoiceRepositoryInterface::class)
            ->give(InvoiceRepository::class);

        $this->app
            ->when(InvoiceRepository::class)
            ->needs(BillRepositoryInterface::class)
            ->give(BillRepository::class);

        $this->app
            ->when(InvoiceBusiness::class)
            ->needs(CreditCardBusinessInterface::class)
            ->give(CreditCardBusiness::class);

        $this->app
            ->when(InvoiceService::class)
            ->needs(InvoiceBusinessInterface::class)
            ->give(InvoiceBusiness::class);
    }

}
