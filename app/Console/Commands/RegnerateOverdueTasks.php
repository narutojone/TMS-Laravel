<?php

namespace App\Console\Commands;

use App\Frequency;
use App\Repositories\Task\Task;
use App\Repositories\Task\TaskInterface;
use Illuminate\Console\Command;

class RegnerateOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate:overdue-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate tasks that have gone overdue';

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
        $taskRepository = app()->make(TaskInterface::class);

        $tasks = $taskRepository->model()
            ->uncompleted()
            ->overdue()
            ->where('repeating', Task::REPEATING)
            ->where('regenerated', Task::NOT_REGENERATED)
            ->get();

        $regeneratedCount = 0;

        foreach($tasks as $task){
            if($task->reopenings->count() == 0){
                if(is_null($task->end_date) || $task->end_date > (new Frequency($task->frequency))->next($task->deadline)) {
                    $newTask = $taskRepository->regenerate($task);
                    $regeneratedCount++;
                    echo 'Created new task (' . $newTask->id . ') from ' . $task->id . PHP_EOL;
                }

            }
        }

        echo 'Regenerated ' . $regeneratedCount . ' tasks.' . PHP_EOL;
    }
}
