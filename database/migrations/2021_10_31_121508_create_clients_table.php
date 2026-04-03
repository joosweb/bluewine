<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('run')->nullable();
            $table->char('verifying_digit', 1)->nullable();
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('email', 100);
            $table->string('phone', 100)->nullable();
            $table->string('runCompany', 30)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('activity', 100)->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('companyOrPerson')->nullable();
            $table->unsignedBigInteger('fk_user_id')->index('fk_user_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
