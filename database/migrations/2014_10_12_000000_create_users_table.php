<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name', 100);
            $table->string('surname', 100)->nullable();
            $table->string('email')->unique()->index('email_index');
            $table->string('password');
            $table->string('nickname', 100)->nullable()->index('nickname_index');
            $table->text('avatar')->nullable();
            $table->integer('code')->nullable()->index('code_index');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users');
    }
}
