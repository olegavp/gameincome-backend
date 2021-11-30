<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnershipAppealsMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('partnership_appeals_messages', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('appeal_id')->index('appeal_id_index');
            $table->foreign('appeal_id')->references('id')->on('partnership_appeals');
            $table->uuid('user_id')->index('user_id_index')->nullable();
            $table->integer('admin_id')->index('admin_id_index')->nullable();
            $table->text('text');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('partnership_appeals_messages');
    }
}
