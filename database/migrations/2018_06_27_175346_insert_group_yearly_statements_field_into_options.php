<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertGroupYearlyStatementsFieldIntoOptions extends Migration
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
                'order'       => 14,
                'key'         => 'group_yearly_statements_field',
                'name'        => 'Group which sees yearly statements',
                'value'       => '',
                'description' => 'Group of users which see and forced to enter yearly statements field in account settings',
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
