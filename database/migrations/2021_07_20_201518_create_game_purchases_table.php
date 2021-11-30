<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('game_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('seller_id')->index('seller_id_index');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->uuid('key_id')->index('key_id_index');
            $table->foreign('key_id')->references('id')->on('game_keys');
            $table->uuid('item_id')->index('item_id');
            $table->foreign('item_id')->references('id')->on('games');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('game_purchases');
    }
}
