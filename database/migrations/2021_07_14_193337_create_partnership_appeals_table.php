<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnershipAppealsTable extends Migration
{
    public function up()
    {
        Schema::create('partnership_appeals', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('number')->index('number_index');
            $table->string('theme')->index('theme_index');
            $table->boolean('answered')->default(0);
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('partnership_appeals');
    }
}
