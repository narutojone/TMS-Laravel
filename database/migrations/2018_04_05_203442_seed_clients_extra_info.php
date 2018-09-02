<?php

use App\Repositories\Client\ClientInterface;
use Illuminate\Database\Migrations\Migration;
use Ixudra\Curl\Facades\Curl;

class SeedClientsExtraInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $endpoint = 'http://data.brreg.no/enhetsregisteret/enhet/{org_number}.json';
        $clientRepository = app()->make(ClientInterface::class);

        $clients = $clientRepository->model()->whereNotNull('organization_number')->where('internal', 0)->where('type', 0)->get();

        foreach($clients as $client) {
            $url = str_replace('{org_number}', $client->organization_number, $endpoint);
            $dataToBeUpdated = [];

            $apiReponse = Curl::to($url)->returnResponseObject()->get();
            $clientDetails = json_decode($apiReponse->content);

            // Fetch type
            if(isset($clientDetails->orgform) && isset($clientDetails->orgform->kode)) {
                $dataToBeUpdated['type'] = $this->castClientType($clientDetails->orgform->kode);
            }

            // Fetch address
            $address = null;
            if(isset($clientDetails->postadresse)) {
                $address = $clientDetails->postadresse;
            }
            elseif(isset($clientDetails->forretningsadresse)) {
                $address = $clientDetails->forretningsadresse;
            }

            if($address !== null) {
                $dataToBeUpdated += [
                    'country_code' => isset($address->landkode) ? $address->landkode : '',
                    'city'         => isset($address->kommune) ? $address->kommune : '',
                    'address'      => isset($address->adresse) ? $address->adresse : '',
                    'postal_code'  => isset($address->postnummer) ? $address->postnummer : '',
                ];
            }

            if(!empty($dataToBeUpdated)) {
                $client->update($dataToBeUpdated);
            }
        }
    }

    protected function castClientType(string $type) : int
    {
        if(strtolower($type) == 'as') return 1;
        if(strtolower($type) == 'enk') return 2;
        if(strtolower($type) == 'da') return 1;     // Handle as AS
        if(strtolower($type) == 'nuf') return 1;    // Handle as AS
        if(strtolower($type) == 'fli') return 1;    // Handle as AS
        if(strtolower($type) == 'ans') return 1;    // Handle as AS
        return 0;
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
