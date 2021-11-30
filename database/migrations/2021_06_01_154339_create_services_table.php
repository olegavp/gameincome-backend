<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name');
            $table->string('slug');
            $table->string('background');
        });
    }


    public function down()
    {
        Schema::dropIfExists('services');
    }
}
