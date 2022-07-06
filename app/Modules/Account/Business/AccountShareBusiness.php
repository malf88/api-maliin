<?php

namespace App\Modules\Account\Business;

use App\Models\User;
use App\Modules\Account\Impl\AccountRepositoryInterface;
use App\Modules\Account\Impl\AccountShareRepositoryInterface;
use App\Modules\Account\Impl\Business\AccountShareBusinessInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountShareBusiness implements AccountShareBusinessInterface
{
    public function __construct(
        private readonly AccountShareRepositoryInterface $accountRepository
    )
    {
    }

    public function findUserInSharedAccount(int $accountId): Collection
    {
        if(!Auth::user()->userIsOwnerAccount($accountId))
            throw new NotFoundHttpException('Registro não encontrado');

        return $this->accountRepository->findUsersSharedByAccount($accountId);

    }
}
