<?php

namespace App\Console\Commands;

use App\Repositories\User\User;
use App\Repositories\UserOutOutOffice\UserOutOutOffice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUsersOutOfOfficeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-ooo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Out of office flag for all users';

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
        // Fetch users that are OOO today
        $usersOutOfOffice = UserOutOutOffice::select('user_id')->where('from_date', '<=', Carbon::now())->where('to_date', '>=', Carbon::now())->groupBy('user_id')->get()->pluck('user_id');

        User::whereIn('id', $usersOutOfOffice)->update(['out_of_office'=>1]);

        User::whereNotIn('id', $usersOutOfOffice)->update(['out_of_office'=>0]);
    }
}
