<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Controllers\InvoiceController;
use App\Modules\Account\Impl\InvoiceRepositoryInterface;
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
            ->when(InvoiceService::class)
            ->needs(InvoiceBusinessInterface::class)
            ->give(InvoiceBusiness::class);
    }

}
