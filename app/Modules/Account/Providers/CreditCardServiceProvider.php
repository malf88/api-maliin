<?php

namespace App\Modules\Account\Providers;

use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Controllers\CreditCardController;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;
use App\Modules\Account\Repository\CreditCardRepository;
use App\Modules\Account\Services\CreditCardService;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;
use Illuminate\Support\ServiceProvider;

class CreditCardServiceProvider extends ServiceProvider
{
    public $bindings = [
        CreditCardBusinessInterface::class => CreditCardBusiness::class,
        CreditCardRepositoryInterface::class => CreditCardRepository::class,
        InvoiceBusinessInterface::class => InvoiceBusiness::class,
    ];

}
