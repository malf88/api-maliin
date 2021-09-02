<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'maliin.invoices';
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
        'pay_day'

    ];
    public function creditcard(){
        return $this->belongsTo('App\CreditCard');
    }
}
