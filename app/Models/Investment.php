<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use SoftDeletes;
    protected $table = 'investments.investments';
    protected $dates = [
        'deleted_at',
        'due_date',
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'amount',
        'date',
        'tax',
        'brokerage',
        'due_date',
        'quantity',
        'stock_id',
        'wallet_id'
    ];
    protected $visible = [
        'id',
        'amount',
        'date',
        'tax',
        'brokerage',
        'due_date',
        'quantity',
        'stock_id',
        'wallet_id'

    ];
    protected $casts = [
        'amount' => 'double',
        'tax' => 'double',
        'brokerage' => 'double',
        'quantity' => 'double'

    ];

    public function wallet(){
        return $this->belongsTo('App\Wallet');
    }

    public function stock(){
        return $this->belongsTo('App\Stock');
    }

    public function liquidates(){
        return $this->hasMany('App\Liquidate');
    }
}
