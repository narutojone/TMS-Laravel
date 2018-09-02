<?php

use App\Repositories\Option\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertRatingTaskOptionsToOptionsTable extends Migration
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
                'key'         => 'user_rating_task_template_bad_rating',
                'name'        => 'User rating task template on bad rating',
                'value'       => '',
                'description' => 'Task template to create when user leaves rating 1 or 2',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'user_rating_task_group',
                'name'        => 'User rating task group',
                'value'       => '',
                'description' => 'User group for task when user leaves rating 1 or 2',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'user_rating_task_deadline',
                'name'        => 'User rating task deadline',
                'value'       => '7',
                'description' => 'Task deadline in number of days from the day rating is made',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_task_template_bad_rating',
                'name'        => 'Client rating task template on bad rating',
                'value'       => '',
                'description' => 'Task template to create when client leaves rating 1 or 2',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_rating_task_group',
                'name'        => 'Client rating task group',
                'value'       => '',
                'description' => 'User group for task when client leaves rating 1 or 2',
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
                'value'       => '7',
                'description' => 'Task deadline in number of days from the day rating is made',
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
