<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriticalItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critical_items', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('fk_user_id')->index('fk_user_id');
            $table->string('code', 50);
            $table->integer('days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('critical_items');
    }
}
