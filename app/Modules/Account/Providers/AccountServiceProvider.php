<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\AccountShareBusiness;
use App\Modules\Account\Business\UserBusiness;
use App\Modules\Account\Controllers\AccountController;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Impl\AccountShareRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\AccountShareBusinessInterface;
use App\Modules\Account\Impl\Business\UserBusinessInterface;
use App\Modules\Account\Impl\UserRepositoryInterface;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\UserRepository;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public $bindings = [
        AccountBusinessInterface::class => AccountBusiness::class,
        AccountRepositoryInterface::class => AccountRepository::class,
        AccountShareRepositoryInterface::class => AccountRepository::class,
        AccountShareBusinessInterface::class => AccountShareBusiness::class,
        UserRepositoryInterface::class => UserRepository::class,
        UserBusinessInterface::class => UserBusiness::class

    ];

}
