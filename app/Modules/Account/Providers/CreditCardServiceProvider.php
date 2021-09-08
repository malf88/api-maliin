<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Bussines\CreditCardBusiness;
use App\Modules\Account\Controllers\CreditCardController;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use App\Modules\Account\Repository\CreditCardRepository;
use App\Modules\Account\Services\CreditCardService;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;
use Illuminate\Support\ServiceProvider;

class CreditCardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(CreditCardController::class)
            ->needs(CreditCardServiceLocal::class)
            ->give(CreditCardService::class);

        $this->app
            ->when(CreditCardBusiness::class)
            ->needs(CreditCardRepositoryInterface::class)
            ->give(CreditCardRepository::class);
    }

}
