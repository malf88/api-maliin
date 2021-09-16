<?php

namespace App\Modules\Account\Services;

use App\Models\CreditCard;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;
use Illuminate\Database\Eloquent\Collection;

class CreditCardService implements CreditCardServiceLocal
{
    private CreditCardBusinessInterface $creditCardBusiness;

    public function __construct(CreditCardBusinessInterface $creditCardBusiness)
    {
        $this->creditCardBusiness = $creditCardBusiness;
    }

    public function getListCreditCardByAccount(int $accountId): Collection
    {
        return $this->creditCardBusiness->getListCreditCardByAccount($accountId);
    }

    public function getCreditCardbyId(int $creditCardId): CreditCard
    {
        return $this->creditCardBusiness->getCreditCardById($creditCardId);
    }

    public function insertCreditCard(int $accountId, array $creditCardData): CreditCard
    {
        return $this->creditCardBusiness->insertCreditCard($accountId,$creditCardData);
    }

    public function updateCreditCard(int $creditCardId, array $creditCardData): CreditCard
    {
        return $this->creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);
    }

    public function removeCreditCard(int $creditCardId): bool
    {
        return $this->creditCardBusiness->removeCreditCard($creditCardId);
    }

    public function getInvoicesByCreditCard(int $creditCardId): Collection
    {
        return $this->creditCardBusiness->getInvoicesByCreditCard($creditCardId);
    }
}
