<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoftwareTable extends Migration
{
    public function up()
    {
        Schema::create('software', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->bigInteger('steam_app_id')->index('steam_app_id');
            $table->string('name')->index('name_index');
            $table->string('developer')->nullable();
            $table->string('publisher')->nullable();
            $table->text('short_description')->nullable();
            $table->text('detailed_description')->nullable();
            $table->string('metacritic')->nullable();
            $table->string('release_date')->nullable();
            $table->string('link_to_media')->nullable();
            $table->text('pc_requirements')->nullable();
            $table->text('header_image')->nullable();
            $table->string('item_type')->default('software')->index('item_type_index');
        });
    }


    public function down()
    {
        Schema::dropIfExists('software');
    }
}
