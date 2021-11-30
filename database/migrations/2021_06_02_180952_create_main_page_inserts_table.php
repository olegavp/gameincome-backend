<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageInsertsTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_inserts', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('over_insert', 100)->nullable();
            $table->string('small_description', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('text_on_button', 30)->nullable();
            $table->string('link')->nullable();
            $table->string('background');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_inserts');
    }
}
