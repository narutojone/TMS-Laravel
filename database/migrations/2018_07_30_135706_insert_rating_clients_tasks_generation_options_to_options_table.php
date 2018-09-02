<?php

use App\Repositories\Option\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertRatingClientsTasksGenerationOptionsToOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $counter = Option::count() + 1;

        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_task_template',
                'name'        => 'Client rating task template',
                'value'       => '108',
                'description' => 'Task template to create a task when client hits completed tasks count (option)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_task_deadline',
                'name'        => 'Client rating task deadline',
                'value'       => '5',
                'description' => 'Deadline (in days) from when the task is created to when it should have deadline.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_tasks_count',
                'name'        => 'Client rating tasks count',
                'value'       => '5',
                'description' => 'Number of completed tasks on client after which an automatic rating task will be created',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_interval',
                'name'        => 'Client rating interval',
                'value'       => '120',
                'description' => 'Interval from last rating when an automatic rating task will not be created',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
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
