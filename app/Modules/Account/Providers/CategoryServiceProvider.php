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
    public $bindings = [
        CategoryBusinessInterface::class => CategoryBusiness::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,

    ];
   
}
