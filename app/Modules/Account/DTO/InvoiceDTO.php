<?php

namespace App\Modules\Account\DTO;

use App\Abstracts\DTOAbstract;
use App\Models\CreditCard;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;

class InvoiceDTO extends DTOAbstract
{
    public int|null $id;
    public string|Carbon|null $start_date;
    public string|Carbon|null $end_date;
    public string|Carbon|null $due_date;
    public string|Carbon|null $pay_day;
    public int|null $credit_card_id;
    public int|null $month_reference;
    public Collection|null $bills;
    public float|null $total_balance;
    public CreditCardDTO|null $credit_card;


}
