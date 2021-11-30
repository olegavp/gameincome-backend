<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageSliderTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_slider', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name', 40);
            $table->string('small_description', 80);
            $table->string('description', 200);
            $table->string('link')->nullable();
            $table->string('text_on_button', 30)->nullable();
            $table->string('preview_background');
            $table->string('background');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_slider');
    }
}
