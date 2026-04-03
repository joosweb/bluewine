<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBsaleConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bsale_config', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_user_id')->primary();
            $table->string('url_documents')->nullable()->default('https://api.bsale.cl/v1/documents.json');
            $table->tinyInteger('invoice_id_letter')->nullable()->default(5);
            $table->tinyInteger('invoice_id_thermal')->nullable()->default(6);
            $table->tinyInteger('ticket_id_letter')->nullable()->default(22);
            $table->tinyInteger('ticket_id_thermal')->nullable()->default(1);
            $table->tinyInteger('quotation_id_letter')->default(24);
            $table->string('token_production')->nullable()->default('f9ea741f4afe087cdf7e90472768bfb59cdca155');
            $table->string('token_certification')->nullable()->default('f9ea741f4afe087cdf7e90472768bfb59cdca155');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsale_config');
    }
}
