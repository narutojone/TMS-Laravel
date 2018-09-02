<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Option\Option;

class InsertClientNotPaidOptionsToOptionsTable extends Migration
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
                'key'         => 'client_not_paid_template',
                'name'        => 'Client not paid task template',
                'value'       => '',
                'description' => 'Task template to create when client marked as not paid',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_not_paid_user_group',
                'name'        => 'Client not paid user group',
                'value'       => '',
                'description' => 'User group for task when client marked as not paid',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_not_paid_deadline_days',
                'name'        => 'Client not paid deadline days',
                'value'       => '7',
                'description' => 'Days until deadline for the task when client marked as not paid',
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
