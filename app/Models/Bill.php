<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $table = 'maliin.bills';
    protected $visible = [
        'description',
        'amount',
        'date',
        'due_date',
        'pay_day',
        'barcode',
        'bill_parent_id',
        'category_id',
        'bill_parent',
        'is_parent',
        'category',
        'account_id',
        'id',
        'credit_card_id',
        'portion'
    ];
    protected $dates = [
        'deleted_at',
        'date',
        'due_date',
        'pay_day'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'amount',
        'date',
        'due_date',
        'pay_day',
        'barcode',
        'bill_parent_id',
        'portion',
        'account_id'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'string',
        'barcode' => 'string'
    ];

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account');
    }
    public function bill_parent(){
        return $this->belongsTo('App\Models\Bill','bill_parent_id');
    }
    public function is_bill_parent(){
        return (Bill::where('bill_parent_id','=',$this->id)->count() > 0);
    }

}
