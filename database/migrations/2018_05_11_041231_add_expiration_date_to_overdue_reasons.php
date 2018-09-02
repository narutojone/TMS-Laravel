<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\TaskOverdueReason\TaskOverdueReasonInterface;

class AddExpirationDateToOverdueReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_overdue_reasons', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->after('comment');
        });

        $taskOverdueReasonsRepository = app()->make(TaskOverdueReasonInterface::class);
        $reasons = $taskOverdueReasonsRepository->all();

        foreach ($reasons as $reason) {
            $reason->update(
                [
                    'expiration_date' => $reason->created_at->addDays($reason->reason->days),
                ]
            );
        }

        Schema::table('task_overdue_reasons', function (Blueprint $table) {
            $table->date('expiration_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('task_overdue_reasons', function (Blueprint $table) {
        //     $table->dropColumn('expiration_date');
        // });
    }
}
