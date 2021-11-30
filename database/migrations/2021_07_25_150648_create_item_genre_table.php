<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemGenreTable extends Migration
{
    public function up()
    {
        Schema::create('item_genre', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('genre_id')->index('genre_id_index');
            $table->string('item_id')->index('item_id_index');
        });
    }


    public function down()
    {
        Schema::dropIfExists('item_genre');
    }
}
