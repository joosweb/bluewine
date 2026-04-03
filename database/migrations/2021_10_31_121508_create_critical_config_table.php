<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriticalConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critical_config', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_user_id')->primary();
            $table->tinyInteger('notification_status')->default(0);
            $table->string('notification_email', 200)->nullable();
            $table->string('notification_range', 15)->default('dias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('critical_config');
    }
}
