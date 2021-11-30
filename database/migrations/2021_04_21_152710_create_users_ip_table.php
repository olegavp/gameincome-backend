<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersIpTable extends Migration
{
    public function up()
    {
        Schema::create('users_ip', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->nullable()->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('token_id')->nullable()->index('token_id_index');
            $table->string('ip')->nullable()->index('ip_index');
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('hash')->nullable()->index('hash_index');
            $table->boolean('confirmed')->default(0)->index('confirmed_index');
            $table->dateTime('confirmed_at')->nullable()->index('confirmed_at_index');
            $table->dateTime('deleted_at')->nullable()->index('deleted_at_index');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users_ip');
    }
}
