<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Task\TaskInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendTasksOverdueReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-overdue-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind about tasks that will become overdue in 24h';

    /**
     * Tasks repository
     *
     * @var \App\Repositories\Task\EloquentRepositoryTask
     */
	protected $tasksRepository;

    /**
     * Create a new command instance.
     *
     * @var \App\Repositories\Task\TaskInterface $tasksRepository
     * @return void
     */
    public function __construct(TaskInterface $tasksRepository)
    {
        parent::__construct();
        $this->tasksRepository = $tasksRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifications = $this->tasksRepository->getCountedTasksForSMSReminder();

        foreach ($notifications as $notification) {
            notification('sms')
                ->to($notification->user_phone)
                ->message("Hei! Du har {$notification->tasks_counted} oppgave(r) som behøver oppdatering av deg før kl. 24 i kveld. Du finner de på dashboardet i TMS. Mvh ACGR")
                ->send();
        }
    }
}
