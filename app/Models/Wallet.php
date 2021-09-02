<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;
    protected $table = 'investments.wallets';
    protected $dates = ['created_at','deleted_at','start_date','end_date'];
    protected $fillable = [
        'id',
        'name',
        'start_date',
        'end_date',
        'user_id',
        'created_at'
    ];
    protected $visible = [
        'id',
        'name',
        'start_date',
        'end_date',
        'user_id',
        'created_at'

    ];
    protected $casts = [
        'name' => 'string'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function balances(){
        return $this->hasMany('App\Balance');
    }

    public function investments(){
        return $this->hasMany('App\Investment');
    }
    public function histories(){
        return $this->hasMany('App\History');
    }

    public function historiesMonth(){
        return $this->hasMany('App\HistoryMonth');
    }
}
