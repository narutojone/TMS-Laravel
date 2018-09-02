<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneSystemLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_system_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('call_id', 50)->nullable(false);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->smallInteger('call_duration')->nullable();
            $table->string('media_file')->nullable();
            $table->smallInteger('media_duration')->nullable();
            $table->unsignedBigInteger('from')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->unsignedInteger('employee_id')->nullable();
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->onDelete('set null');

            $table->foreign('employee_id')
                ->references('id')->on('users')
                ->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_system_logs');
    }
}
