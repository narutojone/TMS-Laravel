<?php

use App\Repositories\Contact\ContactInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Client\ClientInterface;
use App\Repositories\ContactPhone\ContactPhoneInterface;
use App\Repositories\ContactEmail\ContactEmailInterface;

class SeedContactPersons extends Migration
{

    private $clientRepository;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_contact', function (Blueprint $table) {
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('contact_id');
        });

        $contacts = [];
        $companies = [];
        $errors = [];

        $contactRepository = app()->make(ContactInterface::class);
        $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
        $contactEmailRepository = app()->make(ContactEmailInterface::class);
        $this->clientRepository = app()->make(ClientInterface::class);

        $portalClients = DB::table('API_companies')->select('id', 'organization_number')->get();
        foreach ($portalClients as $portalClient) {
            $companies[$portalClient->id] = $portalClient->organization_number;
        }

        $portalContactPersons = DB::table('API_company_contacts')->select('company_id', 'name', 'email', 'phone')->get();
        foreach($portalContactPersons as $portalContactPerson) {
            if(!isset($contacts[$portalContactPerson->name])) {
                $contacts[$portalContactPerson->name] = [];
                if(trim($portalContactPerson->email) != '') {
                    $contacts[$portalContactPerson->name]['email'][] = trim($portalContactPerson->email);
                }
                if(trim($portalContactPerson->phone) != '') {
                    $contacts[$portalContactPerson->name]['phone'][] = trim($portalContactPerson->phone);
                }
            }
            else {
                if(!in_array($portalContactPerson->email, $contacts[$portalContactPerson->name]['email'])) {
                    if(trim($portalContactPerson->email) != '') {
                        $contacts[$portalContactPerson->name]['email'][] = trim($portalContactPerson->email);
                    }
                }

                if(!in_array($portalContactPerson->phone, $contacts[$portalContactPerson->name]['phone'])) {
                    if(trim($portalContactPerson->phone) != '') {
                        $contacts[$portalContactPerson->name]['phone'][] = trim($portalContactPerson->phone);
                    }
                }
            }
            $contacts[$portalContactPerson->name]['client_id'][] = $portalContactPerson->company_id;
        }

        // Insert contacts with phones and emails
        foreach ($contacts as $contactName => $contactDetails) {

            try {
                $contact = $contactRepository->create([
                    'name'   => trim($contactName),
                    'notes'  => '',
                    'active' => 1,
                ]);
            }
            catch (\Exception $e) {
                $errors['contacts'][] = "Failed to insert contact: {$contactName}";
            }

            // Insert phones
            if(isset($contactDetails['phone'])) {
                foreach ($contactDetails['phone'] as $contactPhone) {
                    try {
                        $contactPhoneRepository->create([
                            'contact_id' => $contact->id,
                            'number'     => ltrim($contactPhone, '+'),
                        ]);
                    }
                    catch (\Exception $e) {
                        $errors['contact_phones'][] = "Failed to insert contact phone '{$contactPhone}' for contact '{$contactName}'";
                    }
                }
            }

            // Insert emails
            if(isset($contactDetails['email'])) {
                foreach ($contactDetails['email'] as $emailAddress) {
                    try {
                        $contactEmailRepository->create([
                            'contact_id' => $contact->id,
                            'address'    => $emailAddress,
                        ]);
                    }
                    catch (\Exception $e) {
                        $errors['contact_phones'][] = "Failed to insert contact email '{$emailAddress}' for contact '{$contactName}'";
                    }
                }
            }

            // Update all clients with contact persons
            $clientsToSync = [];
            if(isset($contactDetails['client_id'])) {
                foreach ($contactDetails['client_id'] as $clientId) {
                    if(isset($companies[$clientId])) {
                        $tmsClient = $this->getTmsClient($companies[$clientId]);
                        if ($tmsClient === null) {
                            continue;
                        }

                        $clientsToSync[] = $tmsClient->id;
                    }
                }

                // Update client with the new contact person
                try {
                    $contact->clients()->sync($clientsToSync);
                }
                catch (\Exception $e) {
                    $errors['contact_phones'][] = "Failed to sync Contact '{$contactName}' with clients " . implode(', ', $clientsToSync);
                }
            }
        }

        // dd($errors);
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

    protected function getTmsClient(int $organizationNumber)
    {
        $client = $this->clientRepository->model()->where('organization_number', $organizationNumber)->first();
        if(!$client) {
            return null;
        }

        return $client;
    }
}
