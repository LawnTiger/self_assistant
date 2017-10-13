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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('inner_password')->nullable();
            $table->string('api_token', 60)->unique();
            $table->rememberToken();
            $table->timestamps();
        });
        
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->double('cost', 10, 2);
            $table->date('date');
            $table->string('situation');
            $table->timestamps();
        });
        
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
        
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->double('lat', 15, 8);
            $table->double('lng', 15, 8);
            $table->string('remarks');
            $table->timestamps();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->string('content');
            $table->datetime('time');
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('billings');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('locations');
    }
}
