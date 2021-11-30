<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('news_comments', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('news_id')->index('news_id_index');
            $table->foreign('news_id')->references('id')->on('news');
            $table->uuid('user_id')->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('parent_id')->nullable()->index('parent_id_index');
            $table->text('comment_text');
            $table->softDeletes();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('news_comments');
    }
}
