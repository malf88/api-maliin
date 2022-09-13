<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CreditCardDTO extends DTOAbstract
{
    public int|null $id;
    public string|null $name;
    public int|null $due_day;
    public int|null $close_day;
    public Carbon|string|null $invoices_created;
    public Collection|null $bills;
    public Collection|null $invoices;
    public int|null $account_id;
    public Account|null $account;
}
