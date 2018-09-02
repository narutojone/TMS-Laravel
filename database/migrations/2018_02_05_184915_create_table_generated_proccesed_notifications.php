<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGeneratedProccesedNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generated_processed_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_notification_id')->unsigned()->references('id')->on('template_notifications');
            $table->integer('task_id')->unsigned()->references('id')->on('tasks');
            $table->softDeletes();
            $table->timestamps();
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
