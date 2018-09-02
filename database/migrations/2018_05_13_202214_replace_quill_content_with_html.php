<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \DBlackborough\Quill\Render;
use Illuminate\Support\Facades\Storage;

class ReplaceQuillContentWithHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update 'template_subtask_versions'
        // 'while' structure is a workaround because chunk() skips some records
        while (DB::table('template_subtask_versions')->where('description', 'LIKE', '{%')->count() > 0) {
            DB::table('template_subtask_versions')->orderBy('id', 'ASC')->where('description', 'LIKE', '{%')->chunk(20, function ($templateSubtaskVersions) {
                foreach ($templateSubtaskVersions as $templateSubtaskVersion) {
                    if (strlen($templateSubtaskVersion->description) < 28) { // '{"ops":[{"insert":"\n"}]}'
                        $this->updateRecord('template_subtask_versions', $templateSubtaskVersion->id,'description', '');
                        continue;
                    }

                    $quill = new Render($templateSubtaskVersion->description, 'HTML');
                    $output = $quill->render();

                    $newText = preg_replace_callback('@src="([^"]+)"@', "rewriteImgSrcCallback", $output);

                    $this->updateRecord('template_subtask_versions', $templateSubtaskVersion->id, 'description', $newText);
                }
            });
        }

        // Update 'template_versions'
        while (DB::table('template_versions')->where('description', 'LIKE', '{%')->count() > 0) {
            DB::table('template_versions')->orderBy('id', 'ASC')->where('description', 'LIKE', '{%')->chunk(20, function ($templateVersions) {
                foreach ($templateVersions as $templateVersion) {
                    if (strlen($templateVersion->description) < 28) { // '{"ops":[{"insert":"\n"}]}'
                        $this->updateRecord('template_versions', $templateVersion->id, 'description','');
                        continue;
                    }

                    $quill = new Render($templateVersion->description, 'HTML');
                    $output = $quill->render();

                    $newText = preg_replace_callback('@src="([^"]+)"@', "rewriteImgSrcCallback", $output);

                    $this->updateRecord('template_versions', $templateVersion->id, 'description', $newText);
                }
            });
        }

        // Update 'task_details'
        while (DB::table('task_details')->where('description', 'LIKE', '{%')->count() > 0) {
            DB::table('task_details')->orderBy('id', 'ASC')->where('description', 'LIKE', '{%')->chunk(20, function ($taskDetails) {
                foreach ($taskDetails as $taskDetail) {
                    if (strlen($taskDetail->description) < 28) { // '{"ops":[{"insert":"\n"}]}'
                        $this->updateRecord('task_details', $taskDetail->id, 'description','');
                        continue;
                    }
                    $quill = new Render($taskDetail->description, 'HTML');
                    $output = $quill->render();

                    $newText = preg_replace_callback('@src="([^"]+)"@', "rewriteImgSrcCallback", $output);

                    $this->updateRecord('task_details', $taskDetail->id, 'description', $newText);
                }
            });
        }
    }

    private function updateRecord(string $table, int $id, string $field, string $content)
    {
        DB::table($table)->where('id', $id)->update([
            $field => $content,
        ]);
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

function rewriteImgSrcCallback($match)
{
    $data = $match[1];

    // Skip replacing urls
    if(substr( $data, 0, 4 ) === "http"){
        return 'src="' . $data . '"';
    }

    // Skip broken images
    $x = explode('base64,', $data);
    if(!isset($x[1])) {
        return 'src=""';
    }

    $data = base64_decode($x[1]);

    // Generate unique filename
    $fileName = '/editor/images/'.uniqid('', true) . '_m.jpg';

    // Save image on disk
    Storage::disk('uploads')->put($fileName,  $data);

    // Return the path to the new image
    return 'src="' . $fileName . '"';
}
