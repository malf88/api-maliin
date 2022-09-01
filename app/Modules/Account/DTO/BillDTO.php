<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BillDTO extends DTOAbstract
{
    public string|null $description;
    public float|null $amount;
    public Carbon|string|null $date;
    public Carbon|string|null $due_date;
    public Carbon|string|null $pay_day;
    public string|null $barcode;
    public int|null $category_id;
    public int|null $bill_parent_id;
    public int|null $portion;
    public int|null $account_id;
    public int|null $credit_card_id;
    public int|null $id;
    public bool $update_childs = false;
    public Collection|null $bill_parent;
}
