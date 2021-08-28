<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maliin.accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->string('bank',255);
            $table->string('agency',10)->nullable();
            $table->string('account',20)->nullable();
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('maliin.users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account');
    }
}
