<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\User\User;
use Carbon\Carbon;

class ResetAllProfileUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userprofile:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all users profile updates registrations';

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
        // Force all users to go through their profile.
        $result = User::where('updated_profile', 1)
                    ->update(['updated_profile' => 0]);

        $this->info($result . " user profile(s) was reset back to force update state.");
    }
}
