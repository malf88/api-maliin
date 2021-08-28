<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments.news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('stock_id');
            $table->longText('content');
            $table->text('filename');
            $table->date('date');
            $table->enum('rating',['1', '2','3'])->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['stock_id','filename'],'unique_index_news');
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
        Schema::dropIfExists('lm.news');
    }
}
