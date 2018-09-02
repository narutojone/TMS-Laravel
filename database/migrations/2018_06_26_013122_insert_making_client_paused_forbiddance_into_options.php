<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertMakingClientPausedForbiddanceIntoOptions extends Migration
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
                'order'       => 19,
                'key'         => 'client_paused_day_limit',
                'name'        => 'Time limit until a client can be paused again',
                'value'       => '7',
                'description' => 'The time calculated in days as for how long a client have to remain unpaused until it can be paused again.',
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
