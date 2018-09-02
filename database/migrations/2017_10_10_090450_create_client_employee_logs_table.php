<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientEmployeeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_employee_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->tinyInteger('rating')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('removed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_employee_logs');
    }
}
