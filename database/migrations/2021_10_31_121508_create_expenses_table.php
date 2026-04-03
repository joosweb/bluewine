<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('fk_user_id')->index('fk_user_id');
            $table->integer('fk_user_run')->nullable();
            $table->bigInteger('fk_user_shop')->index('fk_user_shop');
            $table->decimal('amount', 13, 0);
            $table->text('commentary');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
