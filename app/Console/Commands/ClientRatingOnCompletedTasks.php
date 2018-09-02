<?php

namespace App\Console\Commands;

use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Task\Task;
use App\Repositories\Task\TaskInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClientRatingOnCompletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:clients-on-completed-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create client ratings tasks when hits number of completed tasks';

    protected $optionRepository;
    protected $clientRepository;
    protected $taskRepository;
    protected $ratingTasksCreated;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->optionRepository = app()->make(OptionInterface::class);
        $this->clientRepository = app()->make(ClientInterface::class);
        $this->taskRepository = app()->make(TaskInterface::class);
        $this->ratingTasksCreated = 0;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ratingTaskTemplateOption = $this->optionRepository->model()->where('key', 'client_rating_task_template')->first();

        if (!$ratingTaskTemplateOption->template) {
            return $this->error("Task template doesn't exists. You can create one in application settings.");
        }
        $ratingTemplate = $ratingTaskTemplateOption->template;
        $tasksCount = $this->optionRepository->model()->where('key', 'client_rating_tasks_count')->first()->value;
        $daysCount = $this->optionRepository->model()->where('key', 'client_rating_interval')->first()->value;

        // Fetch days to deadline on rating task
        $deadlineDays = $this->optionRepository->model()->where('key', 'client_rating_task_deadline')->first()->value;

        $clients = Client::where('active', Client::IS_ACTIVE)
            ->where('paused', Client::NOT_PAUSED)
            ->where('paid', Client::IS_PAID)
            ->with(['tasks', 'ratingsRatingable'])
            ->get();

        foreach ($clients as $client) {
            $completedTasksCount = $client->tasks->where('completed_at', '!=', null)->count();

            // If client has completed more tasks than the appropriate $taskCount from options
            if ($completedTasksCount >= $tasksCount) {
                $uncompletedRatingTask = $client->tasks
                    ->where('template_id', $ratingTemplate->id)
                    ->where('completed_at', null)
                    ->first();
                // Check if the client has an uncompleted rating task, if so, skip creation of a new rating task
                if (!$uncompletedRatingTask) {
                    // If client has no ratings
                    if (!$client->ratingsRatingable->count()) {
                        $this->generateRatingTask($client, $ratingTemplate, $this->ratingTasksCreated, $deadlineDays);
                    } else {
                        $latestRating = $client->ratingsRatingable->sortByDesc('created_at')->first();
                        if ($latestRating->created_at < Carbon::now()->subDays($daysCount)) {
                            $this->generateRatingTask($client, $ratingTemplate, $this->ratingTasksCreated, $deadlineDays);
                        } 
                    }
                }
            }
        }
        $this->info("\nTotal rating tasks created: " . $this->ratingTasksCreated);
    }

    /**
     * @param $client
     * @param $ratingTemplate
     * @param $ratingTasksCreated
     */
    protected function generateRatingTask($client, $ratingTemplate, $ratingTasksCreated, $deadlineDays)
    {
        // Create the rating task with 5 days from today
        $this->taskRepository->create([
            'user_id'     => $client->employee_id,
            'client_id'   => $client->id,
            'template_id' => $ratingTemplate->id,
            'repeating'   => Task::NOT_REPEATING,
            'deadline'    => Carbon::now()->addDays($deadlineDays)->format('Y-m-d H:i:s'),
        ]);
        $this->ratingTasksCreated++;
        $this->info("Rating task created for Client ID: " . $client->id);
    }
}