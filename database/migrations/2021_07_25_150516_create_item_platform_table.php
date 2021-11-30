<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPlatformTable extends Migration
{
    public function up()
    {
        Schema::create('item_platform', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('platform_id')->index('platform_id_index');
            $table->string('item_id')->index('item_id_index');
        });
    }


    public function down()
    {
        Schema::dropIfExists('item_platform');
    }
}
