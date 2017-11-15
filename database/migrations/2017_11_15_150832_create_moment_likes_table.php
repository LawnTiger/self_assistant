<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMomentLikesTable extends Migration
{
    public function up()
    {
        Schema::create('moment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('moment_id');
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moment_likes');
    }
}
