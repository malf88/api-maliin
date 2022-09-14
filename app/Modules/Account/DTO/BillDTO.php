<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BillDTO extends DTOAbstract
{
    public int|string|null $id;
    public string|null $description;
    public float|null $amount;
    public Carbon|string|null $date;
    public Carbon|string|null $due_date;
    public Carbon|string|null $pay_day;
    public string|null $barcode;
    public int|string|null $category_id;
    public int|string|null $bill_parent_id;
    public int|string|null $portion;
    public int|string|null $account_id;
    public int|string|null $credit_card_id;
    public bool $update_childs = false;
    public Collection|null $bill_parent;
}
