<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersBannedTable extends Migration
{
    public function up()
    {
        Schema::create('users_banned', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->nullable()->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('cause')->nullable()->index('cause_index');
            $table->softDeletes();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users_banned');
    }
}
