<?php

use App\Repositories\Subtask\Subtask;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubtasksTableAddTemplateId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('subtasks', function (Blueprint $table) {
			$table->integer('subtaskTemplateId')->unsigned()->after('task_id')->nullable();
		});

		$allSubtasks = Subtask::all();
		foreach($allSubtasks as $subtask) {

			// Delete subtasks that don't belong to a task
			if(!isset($subtask->task)) {
				$subtask->delete();
				continue;
			}

			$subtaskTemplate = TemplateSubtask::where('template_id', $subtask->task->template_id)->where('title','LIKE',$subtask->title)->first();

			if(!$subtaskTemplate) {
				continue;
			}

			$subtask->subtaskTemplateId = $subtaskTemplate->id;
			$subtask->save();
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
