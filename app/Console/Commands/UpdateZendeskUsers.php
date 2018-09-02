<?php

namespace App\Console\Commands;

use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateZendeskUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zendesk:update-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all users from Zendesk and store them locally';

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

        try {
            do {
                $response = Zendesk::users()->findAll([
                    'page' => $page
                ]);
                foreach ($response->users as $user) {
                    if(trim($user->email) != '') {
                        $this->saveUser($user);
                    }
                }
                $page++;
                $nextPage = $response->next_page;
            } while (!is_null($nextPage));
        }
        catch(ApiResponseException $e) {
            return [];
        }
    }

    /**
     * Save zendesk user
     *
     * @param $user
     */
    private function saveUser($user)
    {
        if ($this->userExists($user->id, $user->email)) {
            DB::table('zendesk_users')->where('zendesk_id', $user->id)->update([
                'name'  => $user->name,
                'email' => $user->email,
            ]);
        } else {
            DB::table('zendesk_users')->insert([
                'zendesk_id' => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
            ]);
        }
    }

    /**
     * Check if zendesk user is already present in local DB
     *
     * @param $zendeskUserId
     * @return boolean
     */
    private function userExists($zendeskUserId, $userEmail)
    {
        return DB::table('zendesk_users')->where('zendesk_id', $zendeskUserId)->where('email', '=', $userEmail)->exists();
    }
}
