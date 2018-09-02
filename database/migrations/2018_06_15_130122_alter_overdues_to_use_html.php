<?php

use DBlackborough\Quill\Render;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOverduesToUseHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 'while' structure is a workaround because chunk() skips some records
        while (DB::table('overdue_reasons')->where('description', 'LIKE', '{%')->count() > 0) {
            DB::table('overdue_reasons')->orderBy('id', 'ASC')->where('description', 'LIKE', '{%')->chunk(20, function ($overdueReasons) {
                foreach ($overdueReasons as $overdueReason) {
                    if (strlen($overdueReason->description) < 28) { // '{"ops":[{"insert":"\n"}]}'
                        $this->updateRecord('overdue_reasons', $overdueReason->id,'description', '');
                        continue;
                    }

                    $quill = new Render($overdueReason->description, 'HTML');
                    $output = $quill->render();

                    $newText = preg_replace_callback('@src="([^"]+)"@', "rewriteImgSrcCallback", $output);

                    $this->updateRecord('overdue_reasons', $overdueReason->id, 'description', $newText);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

    private function updateRecord(string $table, int $id, string $field, string $content)
    {
        DB::table($table)->where('id', $id)->update([
            $field => $content,
        ]);
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
