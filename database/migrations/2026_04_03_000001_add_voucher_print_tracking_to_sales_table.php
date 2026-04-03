<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherPrintTrackingToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('voucher_print_count')->default(0)->after('payment_method');
            $table->timestamp('voucher_first_printed_at')->nullable()->after('voucher_print_count');
            $table->unsignedBigInteger('voucher_first_printed_by')->nullable()->after('voucher_first_printed_at');
            $table->timestamp('voucher_last_printed_at')->nullable()->after('voucher_first_printed_by');
            $table->unsignedBigInteger('voucher_last_printed_by')->nullable()->after('voucher_last_printed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'voucher_print_count',
                'voucher_first_printed_at',
                'voucher_first_printed_by',
                'voucher_last_printed_at',
                'voucher_last_printed_by',
            ]);
        });
    }
}
