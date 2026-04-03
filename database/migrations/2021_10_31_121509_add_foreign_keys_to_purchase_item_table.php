<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPurchaseItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_item', function (Blueprint $table) {
            $table->foreign(['fk_id_purchase'], 'purchase_item_ibfk_1')->references(['id'])->on('purchases')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_item', function (Blueprint $table) {
            $table->dropForeign('purchase_item_ibfk_1');
        });
    }
}
