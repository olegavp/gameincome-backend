<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('global_notifications', function (Blueprint $table) {
            $table->uuid('id')->index('id_index');
            $table->string('type', 100)->nullable()->index('type_index');
            $table->string('name', 100)->nullable();
            $table->string('description', 250)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('global_notifications');
    }
}
