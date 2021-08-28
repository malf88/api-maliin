<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEarnings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.earnings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('stock_id');
            $table->decimal('amount',15,3);
            $table->date('date_with');
            $table->date('pay_date');
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
        Schema::dropIfExists('table_earnings');
    }
}
