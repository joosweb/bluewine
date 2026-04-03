<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('fk_user_id')->index('fk_user_id');
            $table->string('code', 100)->index('code');
            $table->string('name', 100);
            $table->bigInteger('category_id')->index('category_id');
            $table->tinyInteger('type_price')->default(1);
            $table->decimal('price', 13, 0)->nullable();
            $table->decimal('purchase_price', 13, 0);
            $table->text('photo')->nullable();
            $table->unsignedMediumInteger('stock')->nullable();
            $table->unsignedInteger('sold')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
