<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewSubtaskModuleTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('subtask_module_templates')->insert([
            'name'          => 'Task generator',
            'description'   => 'Generate a task',
            'template'      => 'task-generator',
            'user_input'    => 0,
            'settings'      => json_encode([]),
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
