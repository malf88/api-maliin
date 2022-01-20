<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends ApiModel
{
    use SoftDeletes;
    protected array $rules = [
        'description'       =>  'required',
        'amount'            =>  'required|numeric',
        'date'              =>  'required|date|before_or_equal:due_date',
        'category_id'       =>  'required|exists:categories,id',
        'bill_parent_id'    =>  'nullable|exists:bills,id',
        'account_id'        =>  'required|exists:accounts,id',
        'credit_card_id'    =>  'exists:credit_cards,id',
        'due_date'          =>  'nullable|required_without:credit_card_id|date|after_or_equal:date'
    ];
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
        'category_id',
        'bill_parent_id',
        'portion',
        'account_id',
        'credit_card_id'
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
        return $this->hasMany(Bill::class,'bill_parent_id','id');
    }
    public function is_bill_parent(){
        return (Bill::where('bill_parent_id','=',$this->id)->count() > 0);
    }

}
