<?php

namespace App\Modules\Auth\Providers;


use App\Modules\Auth\Business\AuthGoogleBusiness;
use App\Modules\Auth\Impl\AuthRepositoryInterface;
use App\Modules\Auth\Impl\Business\AuthBusinessInterface;
use App\Modules\Auth\Repository\AuthRepository;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public $bindings = [
        AuthRepositoryInterface::class => AuthRepository::class,
        AuthBusinessInterface::class => AuthGoogleBusiness::class

    ];

}
