<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTableTmsOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('order')->nullalbe();
            $table->string('key', 255);
            $table->string('name', 255);
            $table->string('value', 255);
            $table->string('description', 255);
            $table->timestamps();
        });

        // Insert initial options
        DB::table('options')->insert([
            [
                'order'       => 1,
                'key'         => 'client_activate_email_template',
                'name'        => 'Client activate email template',
                'value'       => '',
                'description' => 'Email template to send when client is activated in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 2,
                'key'         => 'client_deactivate_email_template',
                'name'        => 'Client deactivate email template',
                'value'       => '',
                'description' => 'Email template to send when client is deactivated in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 3,
                'key'         => 'client_paid_email_template',
                'name'        => 'Client paid email template',
                'value'       => '',
                'description' => 'Email template to send when client is marked as paid in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 4,
                'key'         => 'client_not_paid_email_template',
                'name'        => 'Client activate email template',
                'value'       => '',
                'description' => 'Email template to send when client is marked as not-paid in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 5,
                'key'         => 'client_new_employee',
                'name'        => 'Client new employee email template',
                'value'       => '',
                'description' => 'Email template to send when client changes employee in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 6,
                'key'         => 'client_new_manager',
                'name'        => 'Client new manager email template',
                'value'       => '',
                'description' => 'Email template to send when client changes manager in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 7,
                'key'         => 'client_new_manager_and_employee',
                'name'        => 'Client new manager and employee email template',
                'value'       => '',
                'description' => 'Email template to send when client changes employee and manager in TMS.',
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
