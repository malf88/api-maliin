<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(AccountController::class)
            ->needs(AccountServiceLocal::class)
            ->give(AccountService::class);
    }

}
