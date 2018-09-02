<?php

namespace App\Console\Commands;

use App\Repositories\UserWorkload\UserWorkloadInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LockUserWorkload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:workload-lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lock users workload for the current month';

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
        $userWorkloadRepository = app()->make(UserWorkloadInterface::class);

        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        $userWorkloadRepository->model()->where('year',$currentYear)->where('month', $currentMonth)->update([
            'locked' => true,
        ]);
    }
}
