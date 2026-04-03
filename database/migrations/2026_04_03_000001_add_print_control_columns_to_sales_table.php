<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintControlColumnsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dateTime('printed_original_at')->nullable()->after('created_at');
            $table->unsignedInteger('print_original_count')->default(0)->after('printed_original_at');
            $table->unsignedInteger('reprint_count')->default(0)->after('print_original_count');
            $table->boolean('print_locked')->default(false)->after('reprint_count');
            $table->unsignedBigInteger('print_last_user_id')->nullable()->after('print_locked');
            $table->dateTime('print_last_at')->nullable()->after('print_last_user_id');

            $table->index('printed_original_at');
            $table->index('print_last_user_id');
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
            $table->dropIndex(['printed_original_at']);
            $table->dropIndex(['print_last_user_id']);

            $table->dropColumn([
                'printed_original_at',
                'print_original_count',
                'reprint_count',
                'print_locked',
                'print_last_user_id',
                'print_last_at',
            ]);
        });
    }
}
