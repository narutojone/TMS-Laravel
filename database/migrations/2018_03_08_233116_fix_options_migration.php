<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixOptionsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Truncate table
        DB::table('options')->truncate();

        // Insert initial options
        DB::table('options')->insert([
            [
                'order'       => 1,
                'key'         => 'client_deactivate_email_template',
                'name'        => 'Client deactivate email template',
                'value'       => '',
                'description' => 'Email template to send when client is deactivated in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 2,
                'key'         => 'client_paid_email_template',
                'name'        => 'Client paid email template',
                'value'       => '',
                'description' => 'Email template to send when client is marked as paid in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 3,
                'key'         => 'client_not_paid_email_template',
                'name'        => 'Client not paid email template',
                'value'       => '',
                'description' => 'Email template to send when client is marked as not-paid in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 4,
                'key'         => 'client_new_employee',
                'name'        => 'Client new employee email template',
                'value'       => '',
                'description' => 'Email template to send when client changes employee in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 5,
                'key'         => 'client_new_manager',
                'name'        => 'Client new manager email template',
                'value'       => '',
                'description' => 'Email template to send when client changes manager in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
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
