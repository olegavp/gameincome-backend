<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersBalanceTable extends Migration
{
    public function up()
    {
        Schema::create('users_balance', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->unique()->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('overall_balance')->default(0)->unsigned();
            $table->bigInteger('pending_balance')->default(0)->unsigned();
            $table->bigInteger('available_balance')->default(0)->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_balance');
    }
}
