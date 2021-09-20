<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends ApiModel
{
    use SoftDeletes;
    protected $table = 'investments.earnings';
    protected $dates = [
        'deleted_at',
        'date_with',
        'created_at',
        'updated_at',
        'pay_date'
    ];
    protected $fillable = [
        'amount',
        'date_with',
        'pay_date',
        'stock_id',
    ];
    protected $visible = [
        'amount',
        'date_with',
        'pay_date',
        'stock_id',

    ];
    protected $casts = [
        'amount' => 'double'

    ];


    public function stock(){
        return $this->belongsTo('App\Models\Stock');
    }
}
