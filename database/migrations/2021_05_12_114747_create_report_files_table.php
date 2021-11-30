<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportFilesTable extends Migration
{
    public function up()
    {
        Schema::create('report_files', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->nullable()->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            //$table->uuid('administration_id')->index('administration_id');
            $table->string('path')->index('path_index');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('report_files');
    }
}
