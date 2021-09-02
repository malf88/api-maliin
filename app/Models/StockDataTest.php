<?php

namespace App\Models;

use App\AnalysisCore\AnalysisFundamentei;
use App\Repositories\Stocks\StockFundamenteiRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockDataTest extends Model
{
    use SoftDeletes;
    protected $table = 'investments.stocks_data_tests';
    protected $dates = ['deleted_at','date'];
    protected $fillable = [
        'stock_id',
        'date',
        'price',
        'pl',
        'pvp',
        'psr',
        'dy',
        'pAtivo',
        'pCapGiro',
        'pEbitda',
        'pAtivoCirculanteLiquido',
        'evEbit',
        'evEbitda',
        'mEbitda',
        'mLiquida',
        'liqCorrente',
        'roic',
        'roe',
        'liq2meses',
        'patrLiquido',
        'divBrutaPatrimonio',
        'crescReceita5Anos',
        'status'
    ];
    protected $visible = [
        'id',
        'stock_id',
        'date',
        'price',
        'pl',
        'pvp',
        'psr',
        'dy',
        'pAtivo',
        'pCapGiro',
        'pEbitda',
        'pAtivoCirculanteLiquido',
        'evEbit',
        'evEbitda',
        'mEbitda',
        'mLiquida',
        'liqCorrente',
        'roic',
        'roe',
        'liq2meses',
        'patrLiquido',
        'divBrutaPatrimonio',
        'crescReceita5Anos',
        'status'
    ];
    protected $casts = [
        'date' => 'date',
        'price' => 'double',
        'pl' => 'double',
        'pvp' => 'double',
        'psr' => 'double',
        'dy' => 'double',
        'pAtivo' => 'double',
        'pCapGiro' => 'double',
        'pEbitda' => 'double',
        'pAtivoCirculanteLiquido' => 'double',
        'evEbit' => 'double',
        'evEbitda' => 'double',
        'mEbitda' => 'double',
        'mLiquida' => 'double',
        'liqCorrente' => 'double',
        'roic' => 'double',
        'roe' => 'double',
        'liq2meses' => 'double',
        'patrLiquido' => 'double',
        'divBrutaPatrimonio' => 'double',
        'crescReceita5Anos' => 'double'

    ];

    public function stock(){
        return $this->belongsTo('App\Models\Stock');
    }

    public function json_fundamentei()
    {
        $jsonRepository = new StockFundamenteiRepository();
        $json = $jsonRepository->getStockFundamentei($this->stock_id);

        return $json->json_fundamentei;
    }

}
