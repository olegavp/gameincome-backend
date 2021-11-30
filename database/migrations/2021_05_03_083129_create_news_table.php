<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name', 110)->index('name_index');
            $table->string('description_on_3_words', 50)->nullable()->index('description_on_3_words_index');
            $table->string('small_description', 255)->nullable()->index('small_description_index');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('relation')->nullable();
            $table->string('small_background')->nullable();
            $table->string('background')->nullable();
            $table->softDeletes();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('news');
    }
}
