<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocketMsg extends Migration
{
    public function up()
    {
        Schema::create('socket_msg', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->tinyInteger('type')->default(0)->comment('0-未读, 1-已读');
            $table->string('msg');
        });
    }

    public function down()
    {
        Schema::dropIfExists('socket_msg');
    }
}
