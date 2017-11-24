<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('password');
            $table->string('role')->default('super');
            $table->nullableTimestamps();

            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
