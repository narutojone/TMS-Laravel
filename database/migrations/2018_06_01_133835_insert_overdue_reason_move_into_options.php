<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertOverdueReasonMoveIntoOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert initial options
        DB::table('options')->insert([
            [
                'order'       => 10,
                'key'         => 'overdue_reason_client_move',
                'name'        => 'Overdue reason for client move',
                'value'       => '14',
                'description' => 'Overdue reason assigned at client move.',
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
