<?php

namespace App\Providers;

use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
