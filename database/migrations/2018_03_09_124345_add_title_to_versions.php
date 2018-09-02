<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateVersion\TemplateVersionInterface;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersionInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;

class AddTitleToVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $errors = [];

        Schema::table('template_versions', function (Blueprint $table) {
            $table->unsignedSmallInteger('created_by')->after('version_no')->nullable();
            $table->string('title', 255)->after('created_by');
        });

        Schema::table('template_subtask_versions', function (Blueprint $table) {
            $table->unsignedSmallInteger('created_by')->after('version_no')->nullable();
            $table->string('title', 255)->after('created_by');
        });

        $templateRepository = app()->make(TemplateInterface::class);
        $templateSubtaskRepository = app()->make(TemplateSubtaskInterface::class);
        $templateVersionRepository = app()->make(TemplateVersionInterface::class);
        $templateSubtaskVersionRepository = app()->make(TemplateSubtaskVersionInterface::class);

        // Fill in titles to all template versions
        $templateVersions = $templateVersionRepository->model()->get(['id','template_id','title']);
        foreach($templateVersions as $templateVersion) {
            $title = $templateRepository->find($templateVersion->template_id)->title;
            $templateVersion->update([
                'title' => $title,
            ]);
        }
        unset($templateVersions);

        // Fill in titles to all template subtasks versions
        $templateSubtaskVersions = $templateSubtaskVersionRepository->model()->get(['id','subtask_template_id', 'title']);
        foreach($templateSubtaskVersions as $templateSubtaskVersion) {
            $templateSubtask = $templateSubtaskRepository->model()->where('id', $templateSubtaskVersion->subtask_template_id)->first();
            if(!$templateSubtask) {
                $errors['missing_template_subtask'][] = $templateSubtaskVersion->subtask_template_id;
                continue;
            }
            $templateSubtaskVersion->update([
                'title' => $templateSubtask->title,
            ]);
        }

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
