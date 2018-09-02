<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\TemplateVersion\TemplateVersionInterface;
use App\Repositories\Task\TaskInterface;

class FixVersioningOnCompletedTasks extends Migration
{

    protected $templateVersions = [];
    protected $log = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $taskRepository = app()->make(TaskInterface::class);
        //

        $this->getTemplateVersions();
        $templatesWIthOnlyOneVersion = $this->getTemplatesWithOnlyOneVersion();

        // Get tasks that have only one template version
        $tasks = $taskRepository->model()
            ->whereNotNull('completed_at')
            ->whereIn('template_id', array_keys($templatesWIthOnlyOneVersion))
            ->get();

        // Update tasks that have only 1 version_no
        foreach($tasks as $task) {
            if($task->version_no != 1) {
                $this->log['wrong_version_no'][] = $task->id;

                $task->version_no = 1;
                $task->save();
            }

            if($task->title != $task->version->title) {
                $this->log['wrong_title'][] = $task->id;

                $task = $task->fresh();
                $task->title = $task->version->title;
                $task->save();
            }
        }

        // Get tasks that have multiple versions that don't have reopenings
        $tasks = $taskRepository->model()
            ->whereNotNull('completed_at')
            ->whereNotIn('template_id', array_keys($templatesWIthOnlyOneVersion))
            ->doesntHave('reopenings')
            ->get();

        // Update tasks that have more than one version_no
        foreach($tasks as $task) {
            $templateVersion = $this->getTaskVersionNumber($task);
            if($templateVersion == null) {
                continue; // log saved in above method
            }

            if($task->version_no != $templateVersion) {
                $this->log['wrong_version_no'][] = $task->id;

                $task->version_no = $templateVersion;
                $task->save();
            }

            if($task->title != $task->version->title) {
                $this->log['wrong_title'][] = $task->id;

                $task = $task->fresh();
                $task->title = $task->version->title;
                $task->save();
            }
        }

        // dd($this->log);
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

    protected function getTemplateVersions()
    {
        $templateVersionRepository = app()->make(TemplateVersionInterface::class);
        $templateVersions = $templateVersionRepository->model()->orderBy('template_id', 'ASC')->orderBy('version_no', 'ASC')->get(['id', 'template_id', 'created_at','version_no']);

        foreach ($templateVersions as $templateVersion) {
            $createdAt = $templateVersion->created_at;
            $this->templateVersions[$templateVersion->template_id][$templateVersion->version_no] = $createdAt;
        }
    }

    protected function getTaskVersionNumber($task)
    {
        if(!isset($this->templateVersions[$task->template_id])) {
            $this->log['missing_task_template'] = $task->id;
            return null;
        }

        $finalVersionNo = 1;

        foreach($this->templateVersions[$task->template_id] as $versionNumber => $createdAt) {
            if($task->completed_at >= $createdAt) {
                $finalVersionNo = $versionNumber;
            }
            else {
                return $finalVersionNo;
            }
        }

        return $finalVersionNo;
    }

    protected function getTemplatesWithOnlyOneVersion() : array
    {
        $output = [];
        foreach ($this->templateVersions as $templateId => $templateVersions) {
            if(sizeof($templateVersions) == 1) {
                $output[$templateId] = $templateVersions[1]; // use use index 1 because the first template version is '1'
            }
        }

        return $output;
    }
}
