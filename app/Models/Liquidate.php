<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Liquidate extends Model
{
    use SoftDeletes;
    protected $table = 'investments.liquidates';
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'amount',
        'date',
        'quantity',
        'investment_id',
    ];
    protected $visible = [
        'amount',
        'date',
        'quantity',
        'investment_id',

    ];
    protected $casts = [
        'amount' => 'double'

    ];

    public function investment(){
        return $this->belongsTo('App\Investment');
    }

}
