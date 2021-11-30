<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsViewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews_views', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('review_id')->index('review_id_index');
            $table->foreign('review_id')->references('id')->on('reviews');
            $table->bigInteger('count')->default(0);
        });
    }


    public function down()
    {
        Schema::dropIfExists('reviews_views');
    }
}
