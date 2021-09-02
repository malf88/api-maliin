<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE SCHEMA IF NOT EXISTS  investments');
        DB::unprepared('CREATE SCHEMA IF NOT EXISTS  maliin');
        Schema::create('maliin.users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',255);
            $table->string('last_name',255);
            $table->string('email',255)->unique();
            $table->string('document',255)->unique();
            $table->string('gender',20)->nullable();
            $table->string('phone',15)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text( 'api_token')->unique();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
