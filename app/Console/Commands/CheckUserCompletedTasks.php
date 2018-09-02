<?php

namespace App\Console\Commands;

use App\Repositories\Comment\CommentInterface;
use App\Repositories\Group\GroupInterface;
use App\Repositories\Review\Review;
use App\Repositories\Review\ReviewInterface;
use App\Repositories\ReviewSetting\ReviewSetting;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Template\Template;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\User\User;
use App\Repositories\User\UserInterface;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtask;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtaskInterface;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use App\Repositories\UserCompletedTask\UserCompletedTaskInterface;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Illuminate\Support\Facades\DB;

class CheckUserCompletedTasks extends Command
{
    private $reviewSettingsRepository;

    private $taskRepository;

    private $groupRepository;

    private $userRepository;

    private $reviewRepository;

    private $userCompletedTasksRepo;

    private $userCompletedSubtasksRepo;

    private $commentRepository;

    private $lvl1reviews = 0;

    private $lvl2reviews = 0;

    private $newReviewsForDeclinedTasks = 0;

    private $template;

    private $reviewSetting;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:completed-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tasks completed for users to create reviews.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $this->taskRepository = app()->make(TaskInterface::class);
        $this->groupRepository = app()->make(GroupInterface::class);
        $this->userRepository = app()->make(UserInterface::class);
        $this->reviewRepository = app()->make(ReviewInterface::class);
        $this->userCompletedTasksRepo = app()->make(UserCompletedTaskInterface::class);
        $this->userCompletedSubtasksRepo = app()->make(UserCompletedSubtaskInterface::class);
        $this->commentRepository = app()->make(CommentInterface::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->reviewSetting = $this->reviewSettingsRepository->model()->first();

        // Check users that needs to have their level automatically increases.
        $userLevels = [1, 2];
        $userCompletedTasksGrouped = $this->userCompletedTasksRepo->getUsersForReview($userLevels);

        // Get the template for the review
        $this->template = $this->reviewSetting->template;

        DB::beginTransaction();

        foreach ($userCompletedTasksGrouped as $userToReview) {

            $user = $this->userRepository->find($userToReview->userId);

            // First check if there is already a review for this user with the same level
            $review = $this->reviewRepository->model()
                ->where('user_id', $userToReview->userId)
                ->where('user_level', $user->level)
                ->orderBy('id', 'desc')
                ->first();

            // If a user has a review for it's current level, do not create a new one, instead check the status of the
            // previously declined tasks.
            if ($review) {
                // if review was declined, we need to check the status of the declined tasks, and if they were completed we need to create another review with them
                if ($review->status == Review::STATUS_DECLINED) {
                    $this->createNewReviewsForDeclined($review);
                } else { // The review is in pending or approve state, we need to do nothing
                    continue;
                }
            } else { // check if the  user has enough tasks so that a review can be created, and create one if all conditions are met
                if (is_null($user->level_increased_at)) {
                    if ($userToReview->tasksWithDifferentTemplatesCompleted >= $this->reviewSetting->no_of_tasks_for_level_two) {
                        $this->createReviewForUser($userToReview);
                    }
                } else {
                    $tasksCompletedAfterLastReview = $this->userCompletedTasksRepo->model()->where("created_at", ">", $user->level_increased_at)->get();
                    if ($tasksCompletedAfterLastReview->count() >= $this->reviewSetting->no_of_tasks_for_level_two) {
                        $this->createReviewForUser($userToReview, $user->level_increased_at);
                    }
                }
            }

        }
        DB::commit();

        echo "\n ".$this->lvl1reviews." reviews created for users of level 1.  \n\n";
        echo "\n ".$this->lvl2reviews." reviews created for users of level 2.  \n\n";
        echo "\n ".$this->newReviewsForDeclinedTasks." new reviews for declined and now completed tasks were created.  \n\n";
    }


    /**
     * Create reviews for a user, if the user had a review declined previously.
     * For this to happen, we need to check the reviewed tasks at the previous declined review and check their status.
     * Create the new review with previously declined tasks only when all the tasks were completed so it is easier to manage the db info
     * @param Review $review
     */
    protected function createNewReviewsForDeclined(Review $review)
    {
        $allDeclinedTasksWereCompleted = true;
        $newUserCompletedTasks = [];

        foreach ($review->declinedUserCompletedTasks()->get() as $declinedUserCompletedTask) {
            if (!$declinedUserCompletedTask->task->isComplete()) {
                // If not all previously declined tasks were completed, than break;
                $allDeclinedTasksWereCompleted = false;
            } else {
                // Prepare data for the new user completed tasks
                $declinedUserCompletedTask->status = UserCompletedTask::STATUS_PENDING;

                $data = $declinedUserCompletedTask->toArray();
                $data['subtasks'] = [];
                unset($data["id"], $data['created_at'], $data['updated_at']);

                // Add the subtasks that needs to be created again.
                $userCompletedSubtasks = $declinedUserCompletedTask->userCompletedSubtasks()->get()->toArray();
                foreach ($userCompletedSubtasks as $userCompletedSubtask) {
                    $userCompletedSubtask['status'] = UserCompletedSubtask::STATUS_PENDING;
                    unset($userCompletedSubtask['id'], $userCompletedSubtask['created_at'], $userCompletedSubtask['updated_at']);
                    array_push($data['subtasks'], $userCompletedSubtask);
                }

                array_push($newUserCompletedTasks, $data);
            }
        }

        // If all tasks declined previously were completed again, create new review and add new user completed tasks records
        if ($allDeclinedTasksWereCompleted && !empty($newUserCompletedTasks)) {
            // Create the new review
            $data = $review->toArray();
            $previousReviewer = $data['reviewer_id'];
            unset($data["id"]);
            $data['reviewer_id'] = $this->groupRepository->getUserForReview($review->user_id, $review->user_level);

            if (is_null($data['reviewer_id'])) {
                $message = "\n No users found to be assigned for the second review after the previous one was declined with critical erros.\n" .
                    "Previous reviewer was user with id: ".$previousReviewer."\n" .
                    "Please run the command one more time or make sure that there is another user available for the second review.\n" .
                    "That means that there are other users assigned to second review group and those users cand process the review template.\n";
                throw new ValidationException($message);
            }

            $reviewer = $this->userRepository->find($data['reviewer_id']);
            $newReview = $this->createReview($reviewer, $review->user_id, $review->user_level);

            foreach ($newUserCompletedTasks as $newUserCompletedTask) {
                // Create new user completed task record
                $newUserCompletedTask['review_id'] = $newReview->id;
                $userCompletedTask = $this->userCompletedTasksRepo->create($newUserCompletedTask);
            }

            $this->newReviewsForDeclinedTasks++;
        }
    }

    /**
     * Create a new review.
     *
     * @param User $reviewer
     * @param int $userToReviewId
     * @param int $userLevel
     * @return mixed
     */
    protected function createReview(User $reviewer, int $userToReviewId, int $userLevel)
    {
        if ($reviewer->id == $userToReviewId) {
            $message = "\n The user: ".$reviewer->name." id: ".$reviewer->id." can not review himself. \n Run the command until another user is assigned for this review. \n";
            throw new ValidationException($message);
        }

        // Create task for reviewer.
        $task = $this->taskRepository->create([
          'client_id' => $reviewer->client_id,
          'template_id' => $this->template->id,
          'user_id' => $reviewer->id,
          'version_no' => $this->template->versions->first()->version_no,
          'category' => $this->template->category,
          'title' => $this->template->title,
          'repeating' => false,
          'frequency' => NULL,
          'deadline' => Carbon::now()->addDays($this->reviewSetting->deadline_offset)->format('Y-m-d H:i:s'),
          'private' => false,
        ]);

        // Add comment to the task, to underline that the task was created for a review
        $user = $this->userRepository->find($userToReviewId);
        $this->commentRepository->create([
            'comment'           => 'Review is regarding '.$user->name,
            'user_id'           => $reviewer->id,
            'after_complete'    => false,
            'task_id'           => $task->id,
            'from_review_page'  => false,
        ]);

        $data = [
            'user_id'           => $userToReviewId,
            'user_level'        => $userLevel,
            'reviewer_id'       => $reviewer->id,
            'reviewer_task_id'  => $task->id,
        ];

        return $this->reviewRepository->create($data);
    }

    /**
     * Create a review for a user if all conditions and validations are met.
     *
     * @param UserCompletedTask $userToReview
     * @param null $levelIncreasedAt
     */
    protected function createReviewForUser(UserCompletedTask $userToReview, $levelIncreasedAt = null)
    {
        $user = $this->userRepository->find($userToReview->userId);

        // Get an admin to review the current user
        $reviewerIdForReview = $this->groupRepository->getUserForReview($userToReview->userId, $user->level);
        $reviewer = $this->userRepository->find($reviewerIdForReview);

        // Reviewer not found. This should not be happening.
        if (is_null($reviewerIdForReview)){
            throw new ResourceNotFoundException('\n [31m Reviewer not found! [0m \n', 404);
        }

        if (!$reviewer->canProcessTemplate($this->template)) {
            $message = "\n The user: ".$reviewer->name." id: ".$reviewer->id." can not process the template review. \n Run the command until a user that can process the template is randomly assigned, or make sure the user can process the template!. \n";
            throw new ValidationException($message);
        }

        if (!$reviewer->client_id) {
            $message = "\n The user: ".$reviewer->name." id: ".$reviewer->id." does not have a client_id value. \n Run the command until a user that has client_id is randomly assigned, or make sure the user can process the template!. \n";
            throw new ValidationException($message);
        }

        $review = $this->createReview($reviewer, $userToReview->userId, $user->level);

        //Update user completed tasks with the newly created review id
        if (is_null($levelIncreasedAt)) {
            $userCompletedTasks = $this->userCompletedTasksRepo->model()
                ->where('review_id', '=', NULL)
                ->where('user_id', '=', $userToReview->userId)
                ->get();
        } else {
            $userCompletedTasks = $this->userCompletedTasksRepo->model()
                ->where('review_id', '=', NULL)
                ->where('user_id', '=', $userToReview->userId)
                ->where('created_at', '>', $levelIncreasedAt)
                ->get();
        }

        $userCompletedTasks->each(function ($userCompletedTask, $key) use ($review) {
            $userCompletedTask->review_id = $review->id;
            $userCompletedTask->save();
        });

        if ($user->level == 1) {
            $this->lvl1reviews++;
        } elseif ($user->level == 2) {
            $this->lvl2reviews++;
        }
    }
}
