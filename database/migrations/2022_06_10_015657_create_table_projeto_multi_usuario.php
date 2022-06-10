<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maliin.accounts_users', function (Blueprint $table) {
            $table->bigInteger('account_id');
            $table->bigInteger('user_id');
            $table->foreign('account_id')->references('id')->on('maliin.accounts');
            $table->foreign('user_id')->references('id')->on('maliin.users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maliin.accounts_users');
    }
};
