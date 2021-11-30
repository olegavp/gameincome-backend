<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersVerifyEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('users_verify_emails', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->uuid('user_id')->nullable()->index('user_id_index');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('user_email', 255)->nullable()->index('user_email_index');
            $table->string('user_name', 100)->nullable()->index('user_name_index');
            $table->string('user_surname', 100)->nullable()->index('user_surname_index');
            $table->text('user_avatar')->nullable();
            $table->string('user_password')->nullable();
            $table->integer('code')->nullable()->index('code_index');
            $table->string('hash')->nullable()->index('hash_index');
            $table->boolean('is_verified')->default(0)->index('verified_index');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users_verify_emails');
    }
}
