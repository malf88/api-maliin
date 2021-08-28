<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvestimens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.investments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount',15,3)->comment('Valor pago');
            $table->date('date');
            $table->decimal('tax',15,3)->default(0.000);
            $table->decimal('brokerage',15,3)->default(0.000)->comment('Corretagem');
            $table->date('due_date')->nullable();
            $table->decimal('quantity',15,3);
            $table->bigInteger('stock_id');
            $table->bigInteger('wallet_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('stock_id')->references('id')->on('investments.stocks');
            $table->foreign('wallet_id')->references('id')->on('investments.wallets');
        });

        Schema::table('investments.stocks', function (Blueprint $table) {
            $table->enum('type',['Renda Fixa', 'Renda Variável'])->default('Renda Variável');
            $table->decimal('income',15,3)->nullable()->comment('Rendimento ao ano');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_investimens');
    }
}
