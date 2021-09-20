<?php

namespace App\Models;

use App\Repositories\Stocks\StockFundamenteiRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockFundamentei extends ApiModel
{
    use SoftDeletes;
    protected $table = 'investments.stocks_fundamentei';
    protected $dates = ['deleted_at','created_at','updated_at'];
    protected $fillable = [
        'stock_id',
        'json_fundamentei'
        ];
    protected $visible = [
        'stock_id',
        'json_fundamentei'
    ];
    public function stock(){
        return $this->belongsTo('App\Models\Stock');
    }

}
