<?php

namespace App\Console\Commands;

use Huddle\Zendesk\Facades\Zendesk;
use App\Repositories\Client\Client;
use Illuminate\Console\Command;

class UpdateZendeskClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zendesk:update-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update zendesk ID for each client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $page = 1;
        do {
            $organisationsResponse = Zendesk::organizations()->findAll(['page' => $page]);
            foreach($organisationsResponse->organizations as $organisation) {
                $organizationNumber = $organisation->external_id;
                if(!$organizationNumber || empty($organizationNumber) || is_null($organizationNumber)) continue;

                $client = Client::where('organization_number', $organizationNumber)->first();
                if(!$client) continue; // company is not present in API PORTAL

                $client->zendesk_id = $organisation->id;
                $client->save();
            }

            $page++;
            $nextPage = $organisationsResponse->next_page;
        } while (!is_null($nextPage));
    }
}
