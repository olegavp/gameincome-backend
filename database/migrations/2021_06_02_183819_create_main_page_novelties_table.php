<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageNoveltiesTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_novelties', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('item_id')->index('item_id_index');
            $table->string('item_type')->index('item_type_index');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_novelties');
    }
}
