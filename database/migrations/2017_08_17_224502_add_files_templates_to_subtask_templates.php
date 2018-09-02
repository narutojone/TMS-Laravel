<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilesTemplatesToSubtaskTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// TODO (alex) - check if we still need this table
		Schema::create('subtask_file_templates', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('subtask_id')->unsigned();
			$table->string('original_name', 255);
			$table->text('path');
			$table->timestamps();
		});
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
