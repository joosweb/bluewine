<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_item', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('fk_sales_id')->index('fk_sales_id');
            $table->string('code', 50);
            $table->string('name_item', 100);
            $table->decimal('price', 13, 0);
            $table->mediumInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_item');
    }
}
