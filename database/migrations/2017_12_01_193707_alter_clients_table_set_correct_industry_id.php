<?php

use App\Repositories\Client\Client;
use App\Repositories\System\System;
use Illuminate\Database\Migrations\Migration;

class AlterClientsTableSetCorrectIndustryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $unknown = System::where('name', 'Unknown')->first();

        foreach (Client::all() as $client) {
            if (! $system = System::where('name', $client->industry)->first()) {
                $client->industry = $unknown ? $unknown->id : 0;
            } else {
                $client->industry = $system->id;
            }

            $client->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (Client::all() as $client) {
            if (! $system = System::find($client->industry)) {
                $client->industry = 'Unknown';
            } else {
                $client->industry = $system->name;
            }

            $client->save();
        }
    }
}
