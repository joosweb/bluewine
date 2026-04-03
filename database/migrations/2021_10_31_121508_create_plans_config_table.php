<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans_config', function (Blueprint $table) {
            $table->integer('id_fk_plan')->primary();
            $table->integer('clients_max');
            $table->integer('items_max');
            $table->integer('prov_max');
            $table->integer('cat_max');
            $table->integer('sellers_max');
            $table->decimal('price', 13, 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans_config');
    }
}
