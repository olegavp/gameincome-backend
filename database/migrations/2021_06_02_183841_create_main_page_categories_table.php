<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('category_id')->index('category_id_index');
            $table->string('background');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_categories');
    }
}
