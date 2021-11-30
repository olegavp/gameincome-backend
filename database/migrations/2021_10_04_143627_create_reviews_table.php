<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name')->index('name_index');
            $table->text('description');
            $table->uuid('item_id')->index('item_id_index');
            $table->string('item_type')->index('type_index');
            $table->uuid('writer_id')->index('writer_id_index');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
