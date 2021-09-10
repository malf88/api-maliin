<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Controllers\BillController;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Services\BillService;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Illuminate\Support\ServiceProvider;

class BillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(BillController::class)
            ->needs(BillServiceLocal::class)
            ->give(BillService::class);

        $this->app
            ->when(BillBusiness::class)
            ->needs(BillRepositoryInterface::class)
            ->give(BillRepository::class);
    }

}
