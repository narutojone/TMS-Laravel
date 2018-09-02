<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertClientPausedOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('options')->insert([
            [
                'order'       => 16,
                'key'         => 'client_paused_template',
                'name'        => 'Client paused task template',
                'value'       => '',
                'description' => 'Task template to create when client marked as paused',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]
        ]);

        DB::table('options')->insert([
            [
                'order'       => 17,
                'key'         => 'client_paused_user_group',
                'name'        => 'Client paused user group',
                'value'       => '',
                'description' => 'User group for task when client marked as paused',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]
        ]);

        DB::table('options')->insert([
            [
                'order'       => 18,
                'key'         => 'client_paused_deadline_days',
                'name'        => 'Client paused deadline days',
                'value'       => '7',
                'description' => 'Days until deadline for the task when client marked as paused',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
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
