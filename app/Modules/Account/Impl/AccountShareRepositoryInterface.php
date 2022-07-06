<?php

namespace App\Modules\Account\Impl;

use Illuminate\Support\Collection;

interface AccountShareRepositoryInterface
{
    public function findUsersSharedByAccount(int $accountId):Collection;
}
