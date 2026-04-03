<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyBoxClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_box_closures', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->timestamp('time_start')->useCurrent();
            $table->timestamp('time_end')->default('0000-00-00 00:00:00');
            $table->decimal('initial_balance', 13, 0)->default(0);
            $table->decimal('expenses', 13, 0)->default(0);
            $table->decimal('cash', 13, 0)->default(0);
            $table->decimal('credit', 13, 0)->nullable()->default(0);
            $table->decimal('debit', 13, 0)->default(0);
            $table->decimal('transfer', 13, 0)->default(0);
            $table->decimal('total', 13, 0)->default(0);
            $table->decimal('total_profit', 13, 0)->default(0);
            $table->decimal('total_general', 13, 0)->default(0);
            $table->decimal('cash_on_hand', 13, 0)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->bigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_box_closures');
    }
}
