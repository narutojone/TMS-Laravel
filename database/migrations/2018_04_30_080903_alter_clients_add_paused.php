<?php

use App\Repositories\Option\OptionInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsAddPaused extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedTinyInteger('paused')->default(0)->after('active');
        });

        $optionsRepository = app()->make(OptionInterface::class);

        $optionsRepository->create([
            'order'       => 6,
            'key'         => 'client_paused_email_template',
            'name'        => 'Client paused email template',
            'value'       => 50,
            'description' => 'Email template to send when client is marked as paused in TMS.',
        ]);

        $optionsRepository->create([
            'order'       => 7,
            'key'         => 'client_not_paused_email_template',
            'name'        => 'Client not paused email template',
            'value'       => 41,
            'description' => 'Email template to send when client is marked as not-paused in TMS.',
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
