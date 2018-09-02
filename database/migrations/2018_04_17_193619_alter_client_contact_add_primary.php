<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\Client\ClientInterface;

class AlterClientContactAddPrimary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_contact', function (Blueprint $table) {
            $table->unsignedTinyInteger('primary')->default(0)->after('contact_id');
        });

        $clientRepository = app()->make(ClientInterface::class);
        $clients = $clientRepository->model()->get();

        foreach($clients as $client) {
            if($client->contacts->count() == 1) {
                $client->contacts->first()->pivot->update([
                    'primary' => 1,
                ]);
            }
        }
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
