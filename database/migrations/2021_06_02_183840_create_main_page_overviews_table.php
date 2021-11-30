<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageOverviewsTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_overviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('overviews_id')->index('overviews_id_index');
            $table->foreign('overviews_id')->references('id')->on('overviews');
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_overviews');
    }
}
