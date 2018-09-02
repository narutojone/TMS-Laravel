<?php

use App\Repositories\Client\Client;
use App\Repositories\ClientPhone\ClientPhone;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->index();
            $table->string('number');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDlete('cascade');
        });

        foreach (Client::all() as $client) {
            if ($client->phone) {
                $client->phones()->save(new ClientPhone([
                    'number' => $client->phone
                ]));
            }
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone')->after('employee_id');
        });

        foreach (Client::all() as $client) {
            if ($client->phones()->count()) {
                $client->phone = $client->phones->first()->number;
                $client->save();
            }
        }

        Schema::dropIfExists('client_phones');
    }
}
