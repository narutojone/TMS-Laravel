<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Repositories\Task\Task;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryNotificationReadAtColumnToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('delivered_read_at')->nullable()->after('delivered');
        });

        // Fetch all tasks that are already delivered
        Task::where('delivered', 1)
            ->update([
                'delivered_read_at' => Carbon::now(),
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
