<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('product')->index('product_index');
            $table->uuid('product_id')->index('product_id_index');
            $table->string('item_type')->nullable();
            $table->uuid('key_id')->nullable();
            $table->string('operation')->index('operation_index');
            $table->boolean('action')->index('action_index');
            $table->boolean('available')->index('available_index');
            $table->bigInteger('amount');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
