<?php

use App\Repositories\Task\Task;
use App\Repositories\Template\Template;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\LogTaskUserAcceptance\LogTaskUserAcceptanceInterface;
use App\Repositories\TemplateVersion\TemplateVersionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTemplateVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement( "SET GLOBAL group_concat_max_len = 99999999" );

        Schema::create('template_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id')->nullable(false);
            $table->unsignedInteger('version_no')->nullable(false);
            $table->longText('description');
            $table->timestamps();
        });

        // We need to default version number to 0 because there are custom tasks that don't have template_id
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('version_no')->after('user_id')->default(0);
        });

        Schema::table('logs_task_user_acceptance', function (Blueprint $table) {
            $table->unsignedInteger('version_no')->after('template_id')->nullable();
        });


        $templatesRepository = app()->make(TemplateInterface::class);
        $logTaskUserAcceptanceRepository = app()->make(LogTaskUserAcceptanceInterface::class);
        $templateVersionRepository = app()->make(TemplateVersionInterface::class);

        $globalLog = [];  // Array in which we'll save all errors from later checks

        // Fetch all available templates (including active = 0 for backwards compatibility)
        $templates = $templatesRepository->model()->get();

        $taskCounter = [];
        foreach($templates as $template) {
            // Get all tasks with this template
            $tasksCount = Task::where('template_id', $template->id)->count();
            $taskCounter[$template->id] = $tasksCount;

            $tasks = DB::select("
              SELECT
                description,
                min(created_at) as created,
                GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS ids
              FROM tasks
              WHERE template_id = {$template->id}
              GROUP BY description
              ORDER by created ASC
            ");

            // Same query as above, but without GROUP_CONCAT ; just to double check
            $taskDescriptions = DB::select("SELECT description,min(created_at) as created FROM tasks WHERE template_id = {$template->id} GROUP BY description ORDER by created ASC");

            // Check if query with GROUP_CONCAT returns the same number of results
            if(sizeof($tasks) != sizeof($taskDescriptions)) {
                $globalLog[] = "GROUP_CONCAT returns different number of results for templateId #{$template->id}";
            }

            // Create versions
            $versionNo = 1;
            foreach ($tasks as $taskGroup) {
                $templateVersionRepository->model()->insert([
                    'template_id' => $template->id,
                    'version_no'  => $versionNo,
                    'description' => $taskGroup->description,
                ]);

                DB::update("UPDATE tasks SET version_no = $versionNo WHERE id IN ($taskGroup->ids)");

                $versionNo++;
            }
        }

        // Create versions for templates that don't have any tasks created
        $templatesIdsWithVersions = [];
        $templatesIdsWithVersionsResult = DB::select("SELECT DISTINCT template_id FROM template_versions");
        foreach($templatesIdsWithVersionsResult as $x) {
            $templatesIdsWithVersions[] = $x->template_id;
        }

        $templatesWithNoVersion = Template::query()
            ->whereNotIn('id', $templatesIdsWithVersions)->get();
        foreach($templatesWithNoVersion as $templateWithNoVersion) {
            $templateVersionRepository->model()->insert([
                'template_id' => $templateWithNoVersion->id,
                'version_no'  => 1,
                'description' => $templateWithNoVersion->description,
            ]);
        }



        // Check that all task versions are correct after migration
        $tasksFinal = Task::whereNotNull('template_id')->get();
        foreach($tasksFinal as $taskFinal) {
            // Get task version from versions table
            $version = $templateVersionRepository->model()
                ->where('template_id', $taskFinal->template_id)
                ->where('version_no', $taskFinal->version_no)->first();

            if($version->description != $taskFinal->description) {
                $globalLog[] = "Task #{$taskFinal->id} has wrong description based on versions";
            }
        }

        // Check if latest template version matches current description
        $templates = $templatesRepository->model()->get();
        foreach($templates as $template) {
            $version = DB::table("template_versions")
                ->where('template_id', $template->id)
                ->orderBy('id', 'DESC')->first();
            if(!$version) {
                $globalLog[] = "TemplateId #{$template->id} has no version";
            }
            if($version->description != $template->description) {
                $globalLog[] = "TemplateId #{$template->id} latest version has wrong description";
            }
        }

        // Update task user acceptance logs
        $allAcceptedTerms = $logTaskUserAcceptanceRepository->model()->get();
        foreach($allAcceptedTerms as $termAccepted) {
            $termAcceptedId = $termAccepted->id;
            // verify if term has an existing version
            $templateVersions = $templateVersionRepository->model()->where('template_id', $termAccepted->template_id)->orderBy('id', 'DESC')->get();
            if(!$templateVersions) {
                $globalLog[] = "Terms accepted #{$termAcceptedId} has no version";
            }

            $foundMatch = false;
            foreach($templateVersions as $templateVersion) {
                if($templateVersion->description == json_decode($termAccepted->terms_accepted)->description) {
                    $foundMatch = true;
                    break;
                }
            }
            if (!$foundMatch) {
                $globalLog[] = "Terms accepted #{$termAcceptedId} mismatch description";
            }
            else {
                $termAccepted->version_no = $templateVersion->version_no ;
                $termAccepted->save();
            }
        }

        // var_dump($globalLog);
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
