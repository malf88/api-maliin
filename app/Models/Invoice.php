<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends ApiModel
{
    use SoftDeletes;

    protected $table = 'maliin.invoices';
    protected array $rules = [
        'start_date'        => 'required|date|before:end_date',
        'end_date'          => 'required|date|after:start_date',
        'due_date'          => 'required|date|after:end_date',
        'month_reference'   => 'required|integer',
        'pay_day'           => 'date',
        'credit_card_id'    => 'exists:credit_cards,id'
    ];
    protected $dates = [
        'deleted_at',
        'start_date',
        'end_date',
        'due_date',
        'pay_day'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'due_date',
        'month_reference',
        'pay_day',
        'credit_card_id'

    ];
    public function creditcard(){
        return $this->belongsTo('App\Models\CreditCard');
    }
}
