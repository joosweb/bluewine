<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_config', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_user_id')->primary();
            $table->string('name_ecommerce', 50)->nullable()->default('OSAN-POS');
            $table->tinyInteger('company_billing')->nullable()->default(1);
            $table->tinyInteger('voucher_logo')->default(0);
            $table->integer('continuous_paper_type')->default(58);
            $table->tinyInteger('type_of_environment')->default(2);
            $table->tinyInteger('ticket_default_format')->default(2);
            $table->tinyInteger('invoice_default_format')->nullable()->default(2);
            $table->tinyInteger('default_document_type')->nullable()->default(0);
            $table->tinyInteger('default_payment_method')->nullable()->default(1);
            $table->tinyInteger('printer')->default(0);
            $table->tinyInteger('optional_printer')->nullable()->default(0);
            $table->string('printer_name', 50)->nullable();
            $table->string('default_css', 25)->nullable()->default('voodoo.css');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_config');
    }
}
