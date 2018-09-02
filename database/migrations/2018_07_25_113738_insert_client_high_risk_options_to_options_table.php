<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Option\Option;

class InsertClientHighRiskOptionsToOptionsTable extends Migration
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
                'key'         => 'client_high_risk_template',
                'name'        => 'Client high risk task template',
                'value'       => '',
                'description' => 'Task template to create when client marked as high risk',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_high_risk_user_group',
                'name'        => 'Client high risk user group',
                'value'       => '',
                'description' => 'User group for task when client marked as high risk',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        ]);

        $counter++;
        DB::table('options')->insert([
            [
                'order'       => $counter,
                'key'         => 'client_high_risk_deadline_days',
                'name'        => 'Client high risk deadline days',
                'value'       => '7',
                'description' => 'Days until deadline for the task when client marked as high risk',
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
