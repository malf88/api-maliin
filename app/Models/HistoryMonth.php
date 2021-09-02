<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryMonth extends Model
{
    use SoftDeletes;
    protected $table = 'investments.histories_month';
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'date'
    ];
    protected $fillable = [
        'wallet_id',
        'month',
        'year',
        'original_amount',
        'last_amount',
        'actual_amount',
        'original_ibov',
        'last_ibov',
        'actual_ibov'
    ];

    protected $casts = [
        'original_amount' => 'double',
        'last_amount' => 'double',
        'actual_amount' => 'double'

    ];

    public function wallet(){
        return $this->belongsTo('App\Models\Wallet');
    }
}
