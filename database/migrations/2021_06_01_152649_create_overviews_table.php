<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOverviewsTable extends Migration
{
    public function up()
    {
        Schema::create('overviews', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('overviews');
    }
}
