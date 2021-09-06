<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Controllers\CategoryController;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use App\Modules\Account\Services\CategoryService;
use App\Modules\Account\Services\CategoryServiceLocal;
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
            ->when(CategoryController::class)
            ->needs(CategoryServiceLocal::class)
            ->give(CategoryService::class);
    }

}
