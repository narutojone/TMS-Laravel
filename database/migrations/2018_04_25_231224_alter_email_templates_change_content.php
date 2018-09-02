<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailTemplatesChangeContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE email_templates MODIFY content LONGTEXT');
        DB::statement('ALTER TABLE email_templates MODIFY content_html LONGTEXT');
        DB::statement('ALTER TABLE email_templates MODIFY footer LONGTEXT');
        DB::statement('ALTER TABLE email_templates MODIFY footer_html LONGTEXT');

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
