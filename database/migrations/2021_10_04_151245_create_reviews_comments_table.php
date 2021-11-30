<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews_comments', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('review_id')->index('review_id_index');
            $table->foreign('review_id')->references('id')->on('reviews');
            $table->uuid('user_id')->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('parent_id')->nullable()->index('parent_id_index');
            $table->text('comment_text');
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('reviews_comments');
    }
}
