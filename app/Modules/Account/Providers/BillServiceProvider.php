<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Controllers\BillController;
use App\Modules\Account\Impl\BillRepositoryInterface;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Services\BillService;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
