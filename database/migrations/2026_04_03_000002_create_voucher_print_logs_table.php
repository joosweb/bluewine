<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherPrintLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_print_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->enum('action', ['ORIGINAL', 'REPRINT', 'BLOCKED']);
            $table->string('reason', 255)->nullable();
            $table->unsignedInteger('copy_number')->default(0);
            $table->enum('source', ['POS_AUTO', 'POS_MANUAL', 'ADMIN_PANEL', 'API'])->default('API');
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('shop_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_print_logs');
    }
}
