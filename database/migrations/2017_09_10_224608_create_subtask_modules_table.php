<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubtaskModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('subtask_module_templates', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 255);
			$table->text('description');
			$table->string('template', 255);
			$table->tinyInteger('user_input')->unsigned()->nullable()->default(0);
			$table->text('settings');
			$table->timestamps();
		});

		Schema::create('template_subtasks_modules', function (Blueprint $table) {
			$table->integer('subtask_id')->unsigned();
			$table->integer('subtask_module_id')->unsigned();
			$table->text('settings');
		});

		DB::table('subtask_module_templates')->insert([
			'name' 			=> 'File upload',
			'description' 	=> 'User needs to upload a file in order to complete the subtask',
			'template' 		=> 'file-upload',
			'user_input' 	=> 1,
			'settings' 		=> json_encode([]),
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
