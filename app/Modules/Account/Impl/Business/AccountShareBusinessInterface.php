<?php

namespace App\Modules\Account\Impl\Business;

use Illuminate\Support\Collection;

interface AccountShareBusinessInterface
{
    public function findUserInSharedAccount(int $accountId):Collection;
}
