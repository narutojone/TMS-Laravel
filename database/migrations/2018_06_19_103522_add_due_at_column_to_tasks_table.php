<?php

use App\Repositories\Task\Task;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueAtColumnToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Nullable is removed further down in migration
            $table->dateTime('due_at')->after('deadline')->nullable();
        });

        // Get all tasks with their latest overdue reason if any
        $tasks = Task::select('tasks.*', 'tor.expired_at')
            ->leftJoin(DB::raw('(SELECT * FROM task_overdue_reasons JOIN (SELECT MAX(task_overdue_reasons.id) max_id FROM task_overdue_reasons GROUP BY task_id) AS tor1 ON task_overdue_reasons.id = tor1.max_id) as tor'), 'tasks.id', '=', 'tor.task_id')
            ->leftJoin('overdue_reasons', 'overdue_reasons.id', '=', 'tor.reason_id')
            ->get();

        foreach ($tasks as $task) {

            // Default due_at field
            $due_at = $task->deadline;

            // Try to find a greater due_at field
            if ($task->expired_at) {
                $due_at = $task->expired_at;
            } elseif ($task->completed_at) {
                $due_at = $task->completed_at;
            }

            // Update the task with correct due_at
            Task::where('id', $task->id)
                ->update(['due_at' => $due_at]);
        }

        Schema::table('tasks', function (Blueprint $table) {
            // Set column back to not nullable
            $table->dateTime('due_at')->nullable(false)->change();
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
