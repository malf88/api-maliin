<?php

namespace App\Modules\Account\Impl\Business;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BillStandarizedInterface
{
    public function normalizeListBills(Collection|LengthAwarePaginator $bills):Collection|LengthAwarePaginator;
    public function normalizeBill(Model $bill):Model;
}
