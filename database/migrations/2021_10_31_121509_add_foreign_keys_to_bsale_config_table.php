<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBsaleConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bsale_config', function (Blueprint $table) {
            $table->foreign(['fk_user_id'], 'bsale_config_ibfk_1')->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bsale_config', function (Blueprint $table) {
            $table->dropForeign('bsale_config_ibfk_1');
        });
    }
}
