<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLiquidate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.liquidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount',15,3)->comment('Valor pago');
            $table->decimal('quantity',15,3);
            $table->date('date');
            $table->bigInteger('investment_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('investment_id')->references('id')->on('investments.investments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_liquidate');
    }
}
