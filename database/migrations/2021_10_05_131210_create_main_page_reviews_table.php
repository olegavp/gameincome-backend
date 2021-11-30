<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('review_id')->index('review_id_index');
            $table->foreign('review_id')->references('id')->on('reviews');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_reviews');
    }
}
