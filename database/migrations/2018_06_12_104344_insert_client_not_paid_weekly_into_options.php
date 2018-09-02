<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertClientNotPaidWeeklyIntoOptions extends Migration
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
                'order'       => 15,
                'key'         => 'client_not_paid_weekly_automatic_email_template',
                'name'        => 'Client not paid email template (automatically)',
                'value'       => '',
                'description' => 'Email template to send automatically on each 7th day after client is marked as not-paid in TMS.',
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
