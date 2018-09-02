<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTablesForTasksAndSubtasksAcceptance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_task_user_acceptance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('template_id')->unsigned();
            $table->longText('terms_accepted')->collate('utf8_general_ci');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::create('logs_subtask_user_acceptance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('subtask_template_id')->unsigned();
            $table->longText('terms_accepted')->collate('utf8_general_ci');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
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
