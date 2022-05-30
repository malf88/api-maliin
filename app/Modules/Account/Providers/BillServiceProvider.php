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
    ];
    public function register()
    {
        $this->app
            ->when(BillController::class)
            ->needs(BillServiceLocal::class)
            ->give(BillService::class);

        $this->app
            ->when(BillBusiness::class)
            ->needs(CreditCardBusinessInterface::class)
            ->give(CreditCardBusiness::class);

        $this->app
            ->when(BillBusiness::class)
            ->needs(AccountBusinessInterface::class)
            ->give(AccountBusiness::class);
        $this->app
            ->when(BillBusiness::class)
            ->needs(BillRepositoryInterface::class)
            ->give(BillRepository::class);

        $this->app
            ->when(BillBusiness::class)
            ->needs(BillStandarizedInterface::class)
            ->give(BillStandarizedService::class);

        $this->app
            ->when(BillStandarizedService::class)
            ->needs(BillRepositoryInterface::class)
            ->give(BillRepository::class);

        $this->app
            ->when(BillService::class)
            ->needs(BillBusinessInterface::class)
            ->give(BillBusiness::class);
    }
    public function boot()
    {
//        if (!Collection::hasMacro('paginate')) {
//
//            Collection::macro('paginate',
//                function ($perPage = 15, $page = null, $options = []) {
//                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
//                    return (new LengthAwarePaginator(
//                        $this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))
//                        ->withPath('');
//                });
//        }
    }

}
