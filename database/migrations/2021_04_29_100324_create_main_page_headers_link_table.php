<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPageHeadersLinkTable extends Migration
{
    public function up()
    {
        Schema::create('main_page_headers_link', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name', 8)->unique();
            $table->string('slug', 15)->unique();
        });
    }


    public function down()
    {
        Schema::dropIfExists('main_page_headers_link');
    }
}
