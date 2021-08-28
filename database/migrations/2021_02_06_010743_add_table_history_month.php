<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableHistoryMonth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.histories_month', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('wallet_id');
            $table->integer('month');
            $table->integer('year');
            $table->date('date');
            $table->decimal('original_amount',15,3);
            $table->decimal('last_amount',15,3);
            $table->decimal('actual_amount',15,3);
            $table->decimal('original_ibov',15,3);
            $table->decimal('last_ibov',15,3);
            $table->decimal('actual_ibov',15,3);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['wallet_id','month','year'],'unique_index_histories_month');
            $table->foreign('wallet_id')->references('id')->on('investments.wallets');
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
