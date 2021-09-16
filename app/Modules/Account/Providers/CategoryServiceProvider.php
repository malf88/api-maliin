<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\CategoryBusiness;
use App\Modules\Account\Controllers\CategoryController;
use App\Modules\Account\Impl\Business\CategoryBusinessInterface;
use App\Modules\Account\Impl\CategoryRepositoryInterface;
use App\Modules\Account\Repository\CategoryRepository;
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

        $this->app
            ->when(CategoryService::class)
            ->needs(CategoryBusinessInterface::class)
            ->give(CategoryBusiness::class);
    }

}
