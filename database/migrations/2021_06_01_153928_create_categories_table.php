<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name');
            $table->string('slug');
        });
    }


    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
