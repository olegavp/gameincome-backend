<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->uuid('id')->primary()->index('id_index');
            $table->string('name', 50)->index('name_index');
            $table->integer('count')->default(0);
            $table->integer('money')->nullable();
            $table->dateTime('finish_time')->nullable();
            $table->softDeletes();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }


    public function down()
    {
        Schema::dropIfExists('promo_codes');
    }
}
