<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('task_overdue_reasons', function (Blueprint $table) {
			$table->index('user_id');
			$table->index('task_id');
			$table->index('reason_id');
		});

		Schema::table('subtasks', function (Blueprint $table) {
			$table->index('task_id');
			$table->index('completed_at');
		});

		Schema::table('tasks', function (Blueprint $table) {
			$table->index('user_id');
			$table->index('client_id');
		});

		Schema::table('clients', function (Blueprint $table) {
			$table->index('manager_id');
			$table->index('employee_id');
			$table->index('organization_number');
		});

		Schema::table('tasks_user_acceptance', function (Blueprint $table) {
			$table->index(['type', 'user_id', 'template_id']);
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
