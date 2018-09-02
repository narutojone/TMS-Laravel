<?php

namespace App\Console\Commands;

use App\Repositories\TaskOverdueReason\TaskOverdueReason;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetOverdueActiveReasons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overdue-reasons:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark overdue reasons as inactive if active period has passed';

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
        // $reasonsToBeUpdated will store all IDs of reasons that will need to be deactivated
        // The purpose is to update them all in 1 query
        $reasonsToBeUpdated = [];

        $taskOverdueReasonsRepository = app()->make(TaskOverdueReason::class);

        // Fetch all active overdue reasons
        $taskOverdueReasons = $taskOverdueReasonsRepository->where('active', 1)->get();

        // Interate through all active overdue reasons and check if they expired based on 'days' field from reason template
        foreach($taskOverdueReasons as $taskOverdueReason) {
            if ($taskOverdueReason->expired_at->lt(Carbon::now())) {
                $reasonsToBeUpdated[] = $taskOverdueReason->id;
            }
        }

        $taskOverdueReasonsRepository->whereIn('id', $reasonsToBeUpdated)->update([
            'active' => 0,
        ]);
    }
}
