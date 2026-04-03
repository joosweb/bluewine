<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCriticalConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('critical_config', function (Blueprint $table) {
            $table->foreign(['fk_user_id'], 'critical_config_ibfk_1')->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('critical_config', function (Blueprint $table) {
            $table->dropForeign('critical_config_ibfk_1');
        });
    }
}
