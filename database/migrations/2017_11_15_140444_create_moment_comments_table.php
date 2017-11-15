<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMomentCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('moment_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('moment_id');
            $table->bigInteger('reply_user_id');
            $table->bigInteger('user_id');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moment_comments');
    }
}
