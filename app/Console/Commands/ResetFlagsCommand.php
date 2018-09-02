<?php

namespace App\Console\Commands;

use App\Repositories\User\User;
use App\Repositories\User\UserInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetFlagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flags:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all users flags which date passed.';

    /**
     * ResetFlagsCommand constructor.
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
        $userRepository = app()->make(UserInterface::class);

        $users = User::with('flags')->whereHas('flags', function ($query) {
            $query->whereNotNull('days')
                ->where('active', 1);
        })->get();

        if ($users->count()) {
            foreach ($users as $user) {
                $user->flags->filter(function ($flag) {
                    return $flag->isActive() && !$flag->isEndless();
                })->each(function ($flag) use ($user, $userRepository) {
                    if (Carbon::parse($flag->validTo())->lt(Carbon::now())) {
                        $userRepository->resetFlag($user);
                        $this->info('Flag with id: ' . $flag->id . '(' . $flag->reason . ') was removed for user ' . $user->name . '.');
                    } else {
                        $this->info('Flag with id: ' . $flag->id . '(' . $flag->reason . ') for user: ' . $user->name . ' is valid to ' . $flag->validTo() . '.');
                    }
                });
            }
        } else {
            $this->info('No users matched criteria.');
        }
    }
}
