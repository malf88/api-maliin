<?php

namespace App\Modules\Account\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

 interface AccountServiceLocal
{

    public function getListAllAccounts(User $user): Collection;

    public function getListAllAccountFromLoggedUser(): Collection;

    public function insertAccount(User $user, array $accountInfo): Model;

    public function updateAccount(int $id, array $accountInfo): Model;

    public function deleteAccount(int $id): bool;

    public function getAccountById(int $id):Model;

}
