<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertContractTemplatesOptions extends Migration
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
                'order'       => 8,
                'key'         => 'contract_created_email_template',
                'name'        => 'Contract create email template',
                'value'       => '51',
                'description' => 'Email template to send when a new contract is created in TMS.',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'order'       => 9,
                'key'         => 'contract_updated_email_template',
                'name'        => 'Contract update email template',
                'value'       => '52',
                'description' => 'Email template to send when a contract is updated in TMS.',
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
