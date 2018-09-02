<?php

use DBlackborough\Quill\Render;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlterFaqToUseHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 'while' structure is a workaround because chunk() skips some records
        while (DB::table('faq')->where('content', 'LIKE', '{%')->count() > 0) {
            DB::table('faq')->orderBy('id', 'ASC')->where('content', 'LIKE', '{%')->chunk(20, function ($faqs) {
                foreach ($faqs as $faq) {
                    if (strlen($faq->content) < 28) { // '{"ops":[{"insert":"\n"}]}'
                        $this->updateRecord('faq', $faq->id,'content', '');
                        continue;
                    }
                    $quill = new Render($faq->content, 'HTML');
                    $output = $quill->render();
                    $newText = preg_replace_callback('@src="([^"]+)"@', "rewriteImgSrcCallback", $output);
                    $this->updateRecord('faq', $faq->id, 'content', $newText);
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

