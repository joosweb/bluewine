<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSalesItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_item', function (Blueprint $table) {
            $table->foreign(['fk_sales_id'], 'sales_item_ibfk_1')->references(['id'])->on('sales')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_item', function (Blueprint $table) {
            $table->dropForeign('sales_item_ibfk_1');
        });
    }
}
