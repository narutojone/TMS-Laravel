<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use \App\Repositories\Subtask\Subtask;

class AddVersionToSubtaskTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_subtask_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subtask_template_id')->nullable(false);
            $table->unsignedInteger('version_no')->nullable(false);
            $table->longText('description');
            $table->timestamps();
        });

        Schema::table('subtasks', function (Blueprint $table) {
            $table->unsignedInteger('version_no')->after('subtaskTemplateId')->nullable(false);
        });

        // Fetch all possible subtaskTemplateId's.
        $subtaskTemplateIds = Subtask::select('subtaskTemplateId')->whereNotNull('subtaskTemplateId')->groupBy('subtaskTemplateId')->get()->pluck('subtaskTemplateId');

        // This is needed because GROUP_CONCAT returns a large value
        DB::raw('SET GLOBAL group_concat_max_len = 99999999');

        // Foreach subtask template ID fetch all possible descriptions (from 'subtasks' table).
        foreach ($subtaskTemplateIds as $subtaskTemplateId)
        {
            $subtaskTemplates = DB::select("
              SELECT
                description,
                min(created_at) as created,
                GROUP_CONCAT(id SEPARATOR ',') as ids
              FROM subtasks
              WHERE subtaskTemplateId = $subtaskTemplateId
              GROUP BY description
              ORDER by created ASC
            ");

            $version = 1;
            foreach($subtaskTemplates as $subtaskTemplate) {
                // Create a new record in 'template_subtask_versions'
                DB::table('template_subtask_versions')->insert([
                    'subtask_template_id' => $subtaskTemplateId,
                    'description'         => $subtaskTemplate->description,
                    'version_no'             => $version,
                    'created_at'          => $subtaskTemplate->created,
                ]);

                // Update all subtasks to use this version
                DB::update("UPDATE subtasks SET version_no = $version WHERE id IN ($subtaskTemplate->ids)");
                $version++;
            }
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
