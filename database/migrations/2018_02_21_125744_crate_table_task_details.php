<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TaskDetails\TaskDetailsInterface;

class CrateTableTaskDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->references('id')->on('tasks');
            $table->longText('description');
            $table->timestamps();
        });

        $taskRepository = app()->make(TaskInterface::class);
        $taskDetailsRepository = app()->make(TaskDetailsInterface::class);

        $customTasks = $taskRepository->model()->whereNull('template_id')->get();
        foreach($customTasks as $customTask) {
            $taskDetailsRepository->create([
                'task_id'     => $customTask->id,
                'description' => $customTask->description,
            ]);
        }
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
