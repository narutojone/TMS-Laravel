<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAcceptanceOfTaskChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('tasks_user_acceptance', function (Blueprint $table) {
			$table->increments('id');
			$table->tinyInteger('type')->unsigned(); // 1=task; 2=subtask
			$table->integer('user_id');
			$table->integer('template_id');
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
