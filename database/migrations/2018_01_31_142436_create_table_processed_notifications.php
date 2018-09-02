<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProcessedNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processed_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_notification_id')->unsigned()->references('id')->on('template_notifications');
            $table->text('data');
            $table->enum('status', ['pending', 'approved', 'declined']);
            $table->boolean('sent')->default(false);
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
