<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class InvoiceDTO extends DTOAbstract
{
    public string|Carbon|null $start_date;
    public string|Carbon|null $end_date;
    public string|Carbon|null $due_date;
    public string|Carbon|null $pay_day;
    public int|null $credit_card_id;
    public int|null $month_reference;
    public Collection|null $bills;


}
