<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'investments.balances';
    protected $dates = ['deleted_at','date'];
    protected $fillable = [
        'id',
        'description',
        'date',
        'wallet_id',
        'amount'
    ];
    protected $visible = [
        'id',
        'description',
        'date',
        'wallet_id',
        'amount'

    ];
    protected $casts = [
        'description' => 'string',
        'amount' => 'double'
    ];
    public function wallet(){
        return $this->belongsTo('App\Wallet');
    }
}
