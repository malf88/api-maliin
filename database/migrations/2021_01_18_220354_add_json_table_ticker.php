<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJsonTableTicker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.stocks_fundamentei', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('stock_id');
            $table->longText('json_fundamentei');
            $table->timestamps();
            $table->softDeletes();
            $table->unique('stock_id','unique_index_stocks_fundamentei');
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
