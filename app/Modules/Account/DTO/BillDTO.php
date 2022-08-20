<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BillDTO extends DTOAbstract
{
    public string $description;
    public float $amount;
    public Carbon|string $date;
    public Carbon|string|null $due_date;
    public Carbon|string|null $pay_day;
    public string $barcode;
    public int $category_id;
    public int|null $bill_parent_id;
    public int $portion;
    public int $account_id;
    public int|null $credit_card_id;
    public int|null $id;
    public bool $update_childs = false;
    public Collection|null $bill_parent;
}
