<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maliin.credit_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->integer('due_day');
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('maliin.users');

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('maliin.categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('maliin.users');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('maliin.bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description',255);
            $table->decimal('amount', 9, 2);
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->date('pay_day')->nullable();
            $table->bigInteger('credit_card_id')->nullable();
            $table->bigInteger('account_id');
            $table->bigInteger('category_id');
            $table->string('barcode',255)->nullable();
            $table->foreign('credit_card_id')->references('id')->on('maliin.credit_cards');
            $table->foreign('account_id')->references('id')->on('maliin.accounts');
            $table->foreign('category_id')->references('id')->on('maliin.categories');
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
        Schema::dropIfExists('maliin.credit_cards');
        Schema::dropIfExists('maliin.categories');
        Schema::dropIfExists('maliin.bills');
    }
}
