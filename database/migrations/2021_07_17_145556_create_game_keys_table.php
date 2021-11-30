<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameKeysTable extends Migration
{
    public function up()
    {
        Schema::create('game_keys', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('seller_id')->index('seller_id_index');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->uuid('item_id')->index('item_id_index');
            $table->string('key')->index('key_index');
            $table->bigInteger('seller_price');
            $table->bigInteger('service_price');
            $table->bigInteger('seller_sale_price')->nullable();
            $table->bigInteger('service_sale_price')->nullable();
            $table->uuid('region_id')->index('region_id_index');
            $table->boolean('bought')->default(0);
            $table->softDeletes();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('game_keys');
    }
}
