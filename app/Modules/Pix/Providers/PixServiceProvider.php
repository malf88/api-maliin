<?php

namespace App\Modules\Pix\Providers;

use App\Modules\Pix\Business\RequestPixBusiness;
use App\Modules\Pix\Impl\Business\RequestPixBusinessInterface;
use Illuminate\Support\ServiceProvider;

class PixServiceProvider extends ServiceProvider
{
    public $bindings = [
        RequestPixBusinessInterface::class => RequestPixBusiness::class
    ];
}
