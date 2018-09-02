<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCompletedSubtasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_completed_subtasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_completed_task_id')->unsigned();
            $table->integer('subtask_id')->unsigned();
            $table->enum('status', ['approved', 'declined', 'pending'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_completed_task_id')->references('id')->on('user_completed_tasks');
            $table->foreign('subtask_id')->references('id')->on('subtasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_completed_subtasks');
    }
}
