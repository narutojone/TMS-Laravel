<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\TaskOverdueReason\TaskOverdueReasonInterface;

class AlterTaskOverdueReasonsAddCounter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_overdue_reasons', function (Blueprint $table) {
            $table->unsignedTinyInteger('counter')->default(1)->after('reason_id');
        });


        $taskOverdueReasonRepository = app()->make(TaskOverdueReasonInterface::class);
        $taskOverdueReasons = $taskOverdueReasonRepository->model()
            ->select('id','task_id', 'reason_id','counter')->orderBy('task_id', 'ASC')->orderBy('id', 'ASC')->get();

        // Iterate from 1 (not from 0) because we assume that the first record should have counter=1
        for($i=1; $i< sizeof($taskOverdueReasons); $i++) {
            // Check if the previous reason was set on the same task
            if($taskOverdueReasons[$i]->task_id == $taskOverdueReasons[$i-1]->task_id) {
                // Check if we have the same overdue reason
                if($taskOverdueReasons[$i]->reason_id == $taskOverdueReasons[$i-1]->reason_id) {
                    // Increment counter on current record
                    $taskOverdueReasons[$i]->update([
                        'counter' => $taskOverdueReasons[$i-1]->counter + 1,
                    ]);
                }
            }
        }
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
