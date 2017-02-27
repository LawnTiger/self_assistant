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
            $table->rememberToken();
            $table->timestamps();
        });
        
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            
        });
        
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            
        });
        
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            
        });
        
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            
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
