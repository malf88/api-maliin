<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableWallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->bigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('maliin.users');

        });
        Schema::create('investments.balances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description');
            $table->dateTime('date');
            $table->bigInteger('wallet_id');
            $table->decimal('amount',15,3);

            $table->timestamps();
            $table->softDeletes();
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
