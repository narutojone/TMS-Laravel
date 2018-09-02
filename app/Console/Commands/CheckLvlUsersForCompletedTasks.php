<?php

namespace App\Console\Commands;

use App\Repositories\Review\ReviewInterface;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\UserCompletedTask\UserCompletedTaskInterface;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class CheckLvlUsersForCompletedTasks extends Command
{
    private $taskRepository;

    private $reviewSettingsRepository;

    private $reviewRepository;

    private $userRepository;

    private $userCompletedTaskRepo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users-for-completed-tasks-lvl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that checks tasks finished by users that are level 1 or 2, in order to create a review for them, for automatic level increase for level 2 or 3.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->taskRepository = app()->make(TaskInterface::class);
        $this->reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $this->reviewRepository = app()->make(ReviewInterface::class);
        $this->userRepository = app()->make(UserInterface::class);
        $this->userCompletedTaskRepo = app()->make(UserCompletedTaskInterface::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reviewSetting = $this->reviewSettingsRepository->model()->first();
        if (!$reviewSetting) {
            echo "\n \e[31m Error: no settings for reviews were found. \e[0m \n";
            exit();
        }
        $reviewSettingTemplates = $reviewSetting->templates->pluck('template_id')->toArray();

        $this->removeDeprecatedReviews();
        
        // Get users that are level 1 and 2 and active
        $users = $this->userRepository->model()->whereIn('level', [1, 2])->active()->get();

        $tasksCompletedProcessedSuccessfully = 0;
        $tasksCompletedProcessedWithErrors = 0;

        foreach ($users as $user) {


            // If the user already has a review for current level
            if ($user->hasReviewForCurrentLevel()->get()->count()) {
                continue;
            }

            // Eager load relations tasks and order it by created_at so we consider for review the newest tasks first
            $user->load(
                [
                    'tasks' => function($query) use ($user) {
                        if ($user->level_increased_at) {
                            $query->where('completed_at', '>', $user->level_increased_at);
                        }
                        $query->orderBy('created_at', 'desc');
                    }
                ]
            );

            foreach ($user->tasks as $task) {
                $userCompletedTask = $this->userCompletedTaskRepo->model()->where('task_id', '=', $task->id)->first();

                // If the task already has a review, skip to the next task.
                // Only one review per task is allowed
                if ($userCompletedTask) {
                    continue;
                }

                // Consider the task for review, if the task is completed and if the template_id associated with the task is added to review settings.
                // Not all the templates needs the be reviewed, so in setting we chose what templates will be reviewed.
                if ($task->isComplete() && in_array($task->template_id, $reviewSettingTemplates)) {
                    // Check if this template already exists in user completed tasks
                    $userCompletedTaskWithTheSameTemplate = $this->userCompletedTaskRepo->model()
                        ->where('user_id', '=', $user->id)
                        ->where('template_id', '=', $task->template_id)
                        ->where('user_level', '=', $user->level)
                        ->first();

                    // Get number of tasks added for review
                    $userCompletedTasks = $this->userCompletedTaskRepo->model()
                        ->where('user_id', '=', $user->id)
                        ->where('user_level', '=', $user->level)
                        ->get();

                    try {
                        // If user does not have a task with the same template id finished and if user does not have enough tasks finished for review
                        if ((!$userCompletedTaskWithTheSameTemplate) && (count($userCompletedTasks) < $reviewSetting->no_of_tasks_for_level_two)) {
                            if ($task->template_id) {
                                $userCompletedTask = $this->userCompletedTaskRepo->create([
                                    'user_id'       => $user->id,
                                    'user_level'    => $user->level,
                                    'task_id'       => $task->id,
                                    'template_id'   => $task->template_id,
                                ]);
                                $tasksCompletedProcessedSuccessfully++;
                            }
                        }
                    } catch (\Exception $e) {
                        $tasksCompletedProcessedWithErrors++;
                        echo "\n".$e->getMessage()."\n";
                        print_r($this->userCompletedTaskRepo->getValidationErrors());
                        echo "\n";
                    }

                }
            }
        }

        if ($tasksCompletedProcessedSuccessfully == 0 && $tasksCompletedProcessedWithErrors == 0) {
            echo "\n\nNo tasks where processed\n\n";
        } else {
            echo "\nTasks processed successfully: ".$tasksCompletedProcessedSuccessfully."\n";
            echo "\nTasks not processed due to errors: ".$tasksCompletedProcessedWithErrors."\n";
        }

    }

    protected function removeDeprecatedReviews()
    {
        $deactivatedUserReviewsDeleted = 0;
        $unassingedUserReviewsDeleted = 0;
        $tasksCompletedDeleted = 0;
        // Get pending reviews
        $reviews = $this->reviewRepository->getReviewsPending();
        foreach ($reviews as $review) {
            $userCompletedTasks = $review->userCompletedTasks()->get();

            //Check if reviewed user is deactivated then delete review and related task
            if (!$review->userReviewed->active) {
                $review->delete();
                $this->taskRepository->delete($review->reviewer_task_id);
                foreach ($userCompletedTasks as $userCompletedTask) {
                    $this->userCompletedTaskRepo->delete($userCompletedTask->id);
                    $tasksCompletedDeleted++;
                }
                $deactivatedUserReviewsDeleted++;
            } else {
                foreach ($userCompletedTasks as $userCompletedTask) {
                    //Check if user was unassigned on the task
                    if ($userCompletedTask->user_id != $userCompletedTask->task->user_id) {
                        $this->userCompletedTaskRepo->delete($userCompletedTask->id);
                        $tasksCompletedDeleted++;
                    }
                }

                //Check if user was unassigned on all the tasks of the review. Then delete review and related task
                if (!$review->userCompletedTasks()->count()) {
                    $review->delete();
                    $this->taskRepository->delete($review->reviewer_task_id);
                    $unassingedUserReviewsDeleted ++;
                }
            }
        }

        echo "\nReview tasks deleted as user was unassigned on the task: " . $tasksCompletedDeleted . "\n";
        echo "\nReviews deleted as user was unassigned on all the tasks of review: " . $unassingedUserReviewsDeleted . "\n";
        echo "\nReviews deleted as user was deactivated: " . $deactivatedUserReviewsDeleted . "\n";
    }
}
