<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertOverdueReasonPaidIntoOptions extends Migration
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
                'order'       => 13,
                'key'         => 'overdue_reason_client_paid',
                'name'        => 'Overdue reason for marking client as paid',
                'value'       => '',
                'description' => 'Overdue reason assigned when client is being marked from unpaid to paid',
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
