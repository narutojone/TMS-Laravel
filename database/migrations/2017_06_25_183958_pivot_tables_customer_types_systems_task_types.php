<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PivotTablesCustomerTypesSystemsTaskTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_customer_types', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('customer_type_id')->unsigned();
        });

        Schema::create('user_systems', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('system_id')->unsigned();
        });

        Schema::create('user_task_types', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('task_type_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
