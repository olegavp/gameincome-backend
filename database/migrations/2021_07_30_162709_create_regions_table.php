<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name')->index('name_index');
            $table->string('slug')->index('slug_index');
        });
    }


    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
