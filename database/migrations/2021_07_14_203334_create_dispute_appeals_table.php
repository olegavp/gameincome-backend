<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputeAppealsTable extends Migration
{

    public function up()
    {
        Schema::create('dispute_appeals', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('item_type', 36);
            $table->uuid('user_id')->index('user_id_index');
            $table->uuid('key_id')->index('key_id_index');
            $table->uuid('seller_id')->index('seller_id_index');
            $table->bigInteger('number')->index('number_index');
            $table->boolean('answered')->default(0);
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });

        Schema::table('dispute_appeals', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }


    public function down()
    {
        Schema::dropIfExists('dispute_appeals');
    }
}
