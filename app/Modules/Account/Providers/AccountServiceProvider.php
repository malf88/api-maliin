<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(AccountController::class)
            ->needs(AccountServiceLocal::class)
            ->give(AccountService::class);
        $this->app
            ->when(AccountService::class)
            ->needs(AccountBusinessInterface::class)
            ->give(AccountBusiness::class);

        $this->app
            ->when(AccountBusiness::class)
            ->needs(AccountRepositoryInterface::class)
            ->give(AccountRepository::class);
    }

}
