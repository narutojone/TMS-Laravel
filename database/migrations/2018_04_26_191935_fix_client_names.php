<?php

use App\Repositories\Client\ClientInterface;
use Illuminate\Database\Migrations\Migration;
use Ixudra\Curl\Facades\Curl;

class FixClientNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fetch the BRREG endpoint and make interface
        $endpoint = 'http://data.brreg.no/enhetsregisteret/enhet/{org_number}.json';
        $clientRepository = app()->make(ClientInterface::class);

        // Fetch all clients that are not internal or does not exsists in API endpoint
        $clients = $clientRepository->model()->whereNotNull('organization_number')->where('internal', 0)->get();

        // Go through each client
        foreach($clients as $client) {
            $url = str_replace('{org_number}', $client->organization_number, $endpoint);
            $dataToBeUpdated = [];

            // Fetch the entity
            $apiReponse = Curl::to($url)->returnResponseObject()->get();
            $clientDetails = json_decode($apiReponse->content);

            // Fetch name
            if(isset($clientDetails->navn) && !empty($clientDetails->navn)) {
            	$client->update(['name' => trim($clientDetails->navn)]);
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
