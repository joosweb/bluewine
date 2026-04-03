<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('run')->nullable();
            $table->char('verifying_digit', 1)->nullable();
            $table->string('name');
            $table->string('last_name', 150)->nullable();
            $table->string('email');
            $table->string('phone', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('facebook_link', 200)->nullable();
            $table->string('company_number', 100)->nullable();
            $table->string('photo')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->timestamp('date_to_pay')->useCurrent();
            $table->integer('fk_id_user_type')->nullable()->default(1);
            $table->integer('fk_id_plan')->default(1);
            $table->bigInteger('shop')->nullable()->index('shop');
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
