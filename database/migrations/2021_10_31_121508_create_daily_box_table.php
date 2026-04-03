<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyBoxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_box', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('daily_box');
            $table->timestamp('created_at')->useCurrent()->unique('created_at');
            $table->string('status', 15)->default('CLOSE');
            $table->bigInteger('fk_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_box');
    }
}
