<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QueryBills extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'maliin.query_bills';

    protected $dates = [
        'date',
        'due_date',
        'pay_day'
    ];
    protected $guarded = [
        'description',
        'amount',
        'is_credit_card',
        'month_reference',
        'date',
        'due_date',
        'pay_day',
        'credit_card_id'
    ];
    protected $visible = [
        'category',
        'description',
        'amount',
        'is_credit_card',
        'month_reference',
        'date',
        'bill_parent_id',
        'due_date',
        'pay_day',
        'credit_card_id',
        'category_id',
        'account_id',
        'id',
        'bill_parent',
        'is_parent',
        'portion'
    ];
    protected $casts = [
        'description' => 'string',
        'amount' => 'double',
        'is_credit_card' => 'boolean',
        'month_reference' => 'integer'
    ];
    public function creditcard(){
        return $this->belongsTo('App\CreditCard');
    }
    public function category(){
        return $this->belongsTo('App\Category');
    }
    public function bill_parent(){
        return $this->belongsTo('App\Bill','bill_parent_id');
    }
    public function is_bill_parent(){
        return (Bill::where('bill_parent_id','=',$this->id)->count() > 0);
    }

}
