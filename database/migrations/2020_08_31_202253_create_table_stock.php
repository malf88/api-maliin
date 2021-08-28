<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE SCHEMA investments');
        Schema::create('investments.stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticker',255);
            $table->decimal('price',15,3);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('investments.stocks_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->bigInteger('stock_id');
            $table->decimal('price',15,2);
            $table->decimal('pl',15,2);
            $table->decimal('pvp',15,2);
            $table->decimal('psr',15,2);
            $table->decimal('dy',15,2);
            $table->decimal('pAtivo',15,2);
            $table->decimal('pCapGiro',15,2);
            $table->decimal('pEbitda',15,2);
            $table->decimal('pAtivoCirculanteLiquido',15,2);
            $table->decimal('evEbit',15,2);
            $table->decimal('evEbitda',15,2);
            $table->decimal('mEbitda',15,2);
            $table->decimal('mLiquida',15,2);
            $table->decimal('liqCorrente',15,2);
            $table->decimal('roic',15,2);
            $table->decimal('roe',15,2);
            $table->decimal('liq2meses',15,2);
            $table->decimal('patrLiquido',15,2);
            $table->decimal('divBrutaPatrimonio',15,2);
            $table->decimal('crescReceita5Anos',15,2);
            $table->enum('status',['Barato', 'Caro', 'Neutro']);
            $table->unique(['stock_id','date'],'unique_index_stock');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('stock_id')->references('id')->on('investments.stocks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
