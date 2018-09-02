<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\Task\TaskInterface;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;
use App\Repositories\Subtask\SubtaskInterface;

class FixExistingVersioning extends Migration
{
    private $templateVersions = [];
    private $templateSubtaskVersions = [];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getLatestTemplatesVersions();
        $this->getLatestTemplateSubtaskVersions();
        $log = [];

        $taskRepository = app()->make(TaskInterface::class);
        $subtaskRepository = app()->make(SubtaskInterface::class);

        $tasks = $taskRepository->model()->uncompleted()->whereNotNull('template_id')->get(['id', 'version_no', 'completed_at', 'template_id']);
        foreach ($tasks as $task) {
            if($task->version_no != $this->templateVersions[$task->template_id]) {
                $task->update([
                    'version_no' => $this->templateVersions[$task->template_id],
                ]);
                $log['tasks'][] = $task->id;
            }
        }

        $subtasks = $subtaskRepository->model()->uncompleted()->get(['id', 'version_no','completed_at', 'subtaskTemplateId']);
        foreach($subtasks as $subtask) {
            if($subtask->version_no != $this->templateSubtaskVersions[$subtask->subtaskTemplateId]) {
                $subtask->update([
                    'version_no' => $this->templateSubtaskVersions[$subtask->subtaskTemplateId],
                ]);
                $log['subtasks'][] = $subtask->id;
            }
        }

        // var_dump($log);
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

    protected function getLatestTemplatesVersions()
    {
        $templateRepository = app()->make(TemplateInterface::class);
        $templates = $templateRepository->all();

        foreach($templates as $template) {
            $this->templateVersions[$template->id] = $template->versions->first()->version_no;
        }
    }

    protected function getLatestTemplateSubtaskVersions()
    {
        $templateSubtaskRepository = app()->make(TemplateSubtaskInterface::class);
        $templateSubtasks = $templateSubtaskRepository->all();

        foreach ($templateSubtasks as $templateSubtask) {
            $this->templateSubtaskVersions[$templateSubtask->id] = $templateSubtask->versions->first()->version_no;
        }
    }
}
