<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersFeedbacksTable extends Migration
{
    public function up()
    {
        Schema::create('sellers_feedbacks', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('seller_id')->index('seller_id');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->uuid('user_id')->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('key_id')->index('key_id');
            $table->integer('rate')->index('rate_id');
            $table->text('comment');
            $table->string('item_type')->index('item_type_index');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('sellers_feedbacks');
    }
}
