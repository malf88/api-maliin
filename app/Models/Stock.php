<?php

namespace App\Models;

use App\Repositories\Stocks\StockFundamenteiRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends ApiModel
{
    use SoftDeletes;
    const RENDA_FIXA = 'Renda Fixa';
    const RENDA_VARIAVEL = 'Renda Variável';
    const TAXA_BOVESPA = 0.030596;

    const INDEX_SELIC = 'SELIC';
    const INDEX_IGPM = 'IGPM';
    const INDEX_IPCA = 'IPCA';
    const INDEX_PREFIXADO = 'Pré-Fixado';
    const INDEX_NAOAPLICAVEL = 'NA';

    protected $table = 'investments.stocks';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id',
        'ticker',
        'price',
        'type',
        'income',
        'index'
    ];

    protected $visible = [
        'id',
        'ticker',
        'price',
        'type',
        'income',
        'index'

    ];
    protected $casts = [
        'ticker' => 'string',
        'price'  => 'double',
        'type' => 'string',
        'income' => 'double'

    ];

    public function stocksData(){
        return $this->hasMany('App\StockData');
    }

    public function investments(){
        return $this->hasMany('App\Investment');
    }

    public function stocksFundamentei(){
        return $this->hasMany('App\Models\StockFundamentei');
    }

    public function earnings(){
        return $this->hasMany('App\Models\Earning');
    }

    public function json_fundamentei()
    {
        $jsonRepository = new StockFundamenteiRepository();
        $json = $jsonRepository->getStockFundamentei($this->stock_id);

        return $json->json_fundamentei;
    }
}
