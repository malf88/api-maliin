<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Bussines\AccountBusiness;
use App\Modules\Account\Bussines\CategoryBusiness;
use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Controllers\CategoryController;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Impl\CategoryRepositoryInterface;
use App\Modules\Account\Respository\AccountRepository;
use App\Modules\Account\Respository\CategoryRepository;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use App\Modules\Account\Services\CategoryService;
use App\Modules\Account\ServicesLocal\CategoryServiceLocal;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app
            ->when(CategoryController::class)
            ->needs(CategoryServiceLocal::class)
            ->give(CategoryService::class);
        $this->app
            ->when(CategoryBusiness::class)
            ->needs(CategoryRepositoryInterface::class)
            ->give(CategoryRepository::class);
    }

}
