<?php

namespace App\Modules\Account\Bussines;

use App\Models\CreditCard;
use App\Modules\Account\Impl\CreditCardRepositoryInterface;

class CreditCardBusiness
{
    private CreditCardRepositoryInterface $CreditCardRepository;
    public function __construct(CreditCardRepositoryInterface $CreditCardRepository)
    {
        $this->CreditCardRepository = $CreditCardRepository;
    }
}
