<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;

class AlterTasksUserAcceptanceAddVersionNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add version_no to user acceptance
        Schema::table('tasks_user_acceptance', function (Blueprint $table) {
            $table->unsignedSmallInteger('version_no')->nullable()->after('template_id');
        });

        $errors = [];

        $taskUserAcceptanceRepository = app()->make(TasksUserAcceptanceInterface::class);
        $templateRepository = app()->make(TemplateInterface::class);
        $templateSubtaskRepository = app()->make(TemplateSubtaskInterface::class);

        // Fill version_no in 'tasks_user_acceptance' table
        $tasksUserAcceptance = $taskUserAcceptanceRepository->all();
        foreach($tasksUserAcceptance as $taskUserAcceptance) {
            if($taskUserAcceptance->type == 1) { // template
                $template = $templateRepository->model()->where('id', $taskUserAcceptance->template_id)->first();
                if($template) {
                    $taskUserAcceptance->update([
                        'version_no' => $template->versions->first()->version_no,
                    ]);
                }
                else {
                    $errors['template'][] = $taskUserAcceptance->template_id;
                }
            }
            else { // template subtask
                $templateSubtask = $templateSubtaskRepository->model()->where('id', $taskUserAcceptance->template_id)->first();
                if($templateSubtask) {
                    $taskUserAcceptance->update([
                        'version_no' => $templateSubtask->versions->first()->version_no,
                    ]);
                }
                else {
                    $errors['templateSubtask'][] = $taskUserAcceptance->template_id;
                }
            }
        }

        // var_dump('AlterTasksUserAcceptanceAddVersionNo');
        // var_dump($errors);
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
