<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount', 13, 0)->nullable();
            $table->decimal('amount', 13, 0);
            $table->bigInteger('fk_cliente_id')->nullable()->index('fk_cliente_id');
            $table->unsignedBigInteger('fk_user_id')->index('fk_user_id');
            $table->bigInteger('fk_shop_id')->index('fk_shop_id');
            $table->tinyInteger('type_document')->nullable();
            $table->string('urlpdf')->nullable();
            $table->bigInteger('folio')->nullable();
            $table->tinyInteger('informedSii')->nullable();
            $table->tinyInteger('payment_method')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
