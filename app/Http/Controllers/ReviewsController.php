<?php

namespace App\Http\Controllers;

use App\Lib\Modules\Modules;
use App\Repositories\Group\GroupInterface;
use App\Repositories\Review\Review;
use App\Repositories\Review\ReviewDeclineRequest;
use App\Repositories\Review\ReviewInterface;
use App\Repositories\Review\ReviewUpdateRequest;
use App\Repositories\ReviewSetting\ReviewSetting;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use App\Repositories\Subtask\SubtaskInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModuleInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtaskInterface;
use App\Repositories\UserCompletedTask\UserCompletedTaskCreateTransformer;
use App\Repositories\UserCompletedTask\UserCompletedTaskInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewsController extends Controller
{
    /**
     * @property UserInterface userRepository
     */
    private $userRepository;
    /**
     * @property TaskInterface taskRepository
     */
    private $taskRepository;

    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $reviewRepository;

    /**
     * @property UserCompletedTaskInterface userCompletedTaskRepository
     */
    private $userCompletedTaskRepository;
    /**
     * @var UserCompletedSubtaskInterface
     */
    private $userCompletedSubtaskRepository;
    /**
     * @var SubtaskInterface
     */
    private $subtaskRepository;

    /**
     * UserController constructor.
     *
     * @param ReviewInterface $reviewRepository
     * @param TaskInterface $taskRepository
     * @param UserCompletedTaskInterface $userCompletedTaskRepository
     * @param UserInterface $userRepository
     * @param UserCompletedSubtaskInterface $userCompletedSubtaskRepository
     * @param SubtaskInterface $subtaskRepository
     */
    public function __construct(
        ReviewInterface $reviewRepository,
        TaskInterface $taskRepository,
        UserCompletedTaskInterface $userCompletedTaskRepository,
        UserInterface $userRepository,
        UserCompletedSubtaskInterface $userCompletedSubtaskRepository,
        SubtaskInterface $subtaskRepository)
    {
        parent::__construct();

        $this->reviewRepository = $reviewRepository;
        $this->taskRepository = $taskRepository;
        $this->userCompletedTaskRepository = $userCompletedTaskRepository;
        $this->userRepository = $userRepository;
        $this->userCompletedSubtaskRepository = $userCompletedSubtaskRepository;
        $this->subtaskRepository = $subtaskRepository;
    }

    /**
     * Shows the list of reviews.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $reviews = $this->reviewRepository->all(['userReviewed', 'reviewerTask'])->sortByDesc('id');

        return view('reviews.list', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Show the list of pending reviews.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pending(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $reviews = $this->reviewRepository->model()
                ->where('status', '=', Review::STATUS_PENDING)
                ->with(['userReviewed', 'reviewerTask'])
                ->get();
        } else {
            $reviews = $this->reviewRepository->model()
                ->where('status', '=', Review::STATUS_PENDING)
                ->where('reviewer_id', '=', auth()->user()->id)
                ->with(['userReviewed', 'reviewerTask'])
                ->get();
        }

        return view('reviews.list', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Show the list of completed reviews.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completed(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $reviews = $this->reviewRepository->model()
                ->where('status', '<>', Review::STATUS_PENDING)
                ->with(['userReviewed', 'reviewerTask'])
                ->get();
        } else {
            $reviews = $this->reviewRepository->model()
                ->where('status', '<>', Review::STATUS_PENDING)
                ->where('reviewer_id', '=', auth()->user()->id)
                ->with(['userReviewed', 'reviewerTask'])
                ->get();
        }

        return view('reviews.list', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Show the individual review page;
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $id)
    {
        $review = $this->reviewRepository->find($id, ['userReviewed', 'reviewerTask']);

        return view('reviews.show', [
            'review' => $review,
        ]);
    }

    /**
     * Show the review page for a task
     *
     * @param int $reviewId
     * @param int $userCompletedTaskId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reviewTask(int $reviewId, int $userCompletedTaskId) {

        $review = $this->reviewRepository->find($reviewId);
        $userCompletedTask = $this->userCompletedTaskRepository->find($userCompletedTaskId);
        $task = $userCompletedTask->task;

        return view('reviews.task-review', [
            'review'            => $review,
            'task'              => $task,
            'userCompletedTask' => $userCompletedTask
        ]);
    }

    /**
     * Approve a review task.
     * If we approve a review task we automatically must approve all reviews for subtasks related to that task
     *
     * @param int $reviewId
     * @param int $userCompletedTask
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function approveTask(int $reviewId, int $userCompletedTask)
    {
        try {
            // Get review.
            $review = $this->reviewRepository->find($reviewId);

            $sameId = $this->reviewRepository->validateReviewerAndUserToReview($review);
            if ($sameId) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('You are not allowed to review yourself.');
            }

            // Check if the current logged user is assigned to the current review.
            if ($review->reviewer_id != auth()->user()->id) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('You are not allowed to review this task.');
            }

            // Check if the review's status is not pending
            // We might be in the situation where a review was already done for this task, but a new review was created and the task was marked as pending.
            // If so the task will be shown in two reviews with status pending, and we must allow only the user that has the new review assigned to approve it.
            if ($review->status != Review::STATUS_PENDING ) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('At least one review was already done for this task and now someone else is/will be assigned to review this task.');
            }

            DB::beginTransaction();
            // Mark task review as approved
            $this->userCompletedTaskRepository->markApproved($userCompletedTask, $review);

            DB::commit();
            return redirect()
                ->action('ReviewsController@show', [$reviewId])
                ->with('success', 'Task review approved.');


        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::rollback();
        return redirect()
            ->action('ReviewsController@show', [$reviewId])
            ->withErrors('An error has occurred.');
    }

    /**
     * Decline a review task.
     * If we decline a review task then we automatically must decline all reviews for subtasks related to that task
     *
     * @param ReviewDeclineRequest $request
     * @param int $reviewId
     * @param int $userCompletedTaskId
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function declineTask(ReviewDeclineRequest $request, int $reviewId, int $userCompletedTaskId)
    {
        DB::beginTransaction();
        try {
            // Get review.
            $review = $this->reviewRepository->find($reviewId);

            $sameId = $this->reviewRepository->validateReviewerAndUserToReview($review);
            if ($sameId) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('You are not allowed to review yourself.');
            }

            // Check if the current logged user is assigned to the current review.
            if ($review->reviewer_id != auth()->user()->id) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('You are not allowed to review this task.');
            }

            // Check if the review's status is not pending
            // We might be in the situation where a review was already done for this task, but a new review was created and the task was marked as pending.
            // If so the task will be shown in two reviews with status pending, and we must allow only the user that has the new review assigned to approve it.
            if ($review->status != Review::STATUS_PENDING ) {
                return redirect()
                    ->action('ReviewsController@show', [$reviewId])
                    ->withErrors('At least one review was already done for this task and now someone else is/will be assigned to review this task.');
            }

            // Mark task review as declined
            $userCompletedTask = $this->userCompletedTaskRepository->markDeclined($userCompletedTaskId, $review);

            // Reopen task;
            $input = $request->all();
            $subtasksIds = $userCompletedTask->task->subtasks->pluck('id')->toArray();
            $input['subtasks'] = $subtasksIds;

            $input['reason'] = 'Det er blitt utført kontroll på denne oppgaven, og følgene avvik må rettes opp: '.$input['reason'];
            $this->taskRepository->reopen($userCompletedTask->task, $request->user()->id, $input);

            DB::commit();
            return redirect()
                ->action('ReviewsController@show', [$reviewId])
                ->with('success', 'Task review declined.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return redirect()
            ->action('ReviewsController@show', [$reviewId])
            ->withErrors('An error has occurred.');
    }

    /**
     * Approve a user completed subtask
     *
     * @param int $reviewId
     * @param int $userCompletedSubtaskId
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function approveSubtask(int $reviewId, int $userCompletedSubtaskId)
    {
        DB::beginTransaction();

        try {
            // Get review.
            $review = $this->reviewRepository->find($reviewId);

            $userCompletedSubtask = $this->userCompletedSubtaskRepository->find($userCompletedSubtaskId);
            $sameId = $this->reviewRepository->validateReviewerAndUserToReview($review);
            if ($sameId) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('You are not allowed to review yourself.');
            }

            // Check if the current logged user is assigned to the current review.
            if ($review->reviewer_id != auth()->user()->id) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('You are not allowed to review this subtask.');
            }

            // Check if the review's status is not pending
            // We might be in the situation where a review was already done for this subtask, but a new review was created and the subtask was marked as pending.
            // If so the subtask will be shown in two reviews with status pending, and we must allow only the user that has the new review assigned to approve it.
            if ($review->status != Review::STATUS_PENDING ) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('At least one review was already done for this task and now someone else is/will be assigned to review this task.');
            }

            // Mark task review as approved
            $userCompletedSubtask = $this->userCompletedSubtaskRepository->markApproved($userCompletedSubtaskId, $review);

            DB::commit();
            return redirect()
                ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                ->with('success', 'Subtask review approved.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::rollback();
        return redirect()
            ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
            ->withErrors('An error has occurred.');
    }

    /**
     * Decline a user completed subtask
     *
     * @param ReviewDeclineRequest $request
     * @param int $reviewId
     * @param int $userCompletedSubtaskId
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function declineSubtask(ReviewDeclineRequest $request, int $reviewId, int $userCompletedSubtaskId)
    {
        DB::beginTransaction();

        try {
            // Get review.
            $review = $this->reviewRepository->find($reviewId);

            $userCompletedSubtask = $this->userCompletedSubtaskRepository->find($userCompletedSubtaskId);
            $sameId = $this->reviewRepository->validateReviewerAndUserToReview($review);
            if ($sameId) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('You are not allowed to review yourself.');
            }


            // Check if the current logged user is assigned to the current review.
            if ($review->reviewer_id != auth()->user()->id) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('You are not allowed to review this subtask.');
            }

            // Check if the review's status is not pending
            // We might be in the situation where a review was already done for this subtask, but a new review was created and the subtask was marked as pending.
            // If so the subtask will be shown in two reviews with status pending, and we must allow only the user that has the new review assigned to decline it.
            if ($review->status != Review::STATUS_PENDING ) {
                return redirect()
                    ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                    ->withErrors('At least one review was already done for this task and now someone else is/will be assigned to review this task.');
            }

            // Mark task review as declined
            $userCompletedSubtask = $this->userCompletedSubtaskRepository->markDeclined($userCompletedSubtaskId, $review);

            // Reopen subtask.
            $data = $request->all();
            $data['reason'] = 'Det er blitt utført kontroll på denne oppgaven, og følgene avvik må rettes opp: '.$data['reason'];
            $subtask = $this->subtaskRepository->createReopenings($userCompletedSubtask->subtask_id, $request->user()->id, $data);
            DB::commit();
            return redirect()
                ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
                ->with('success', 'Subtask declined.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::rollback();
        return redirect()
            ->action('ReviewsController@reviewTask', [$reviewId, $userCompletedSubtask->user_completed_task_id])
            ->withErrors('An error has occurred.');
    }

    /**
     * Show the review page for a user completed subtask.
     *
     * @param int $reviewId
     * @param int $userCompletedTaskId
     * @param int $userCompletedSubtaskId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reviewSubtask(int $reviewId, int $userCompletedTaskId, int $userCompletedSubtaskId)
    {
        $review = $this->reviewRepository->find($reviewId);
        $userCompletedTask = $this->userCompletedTaskRepository->find($userCompletedTaskId);
        $userCompletedSubtask = $this->userCompletedSubtaskRepository->find($userCompletedSubtaskId);


        return view('reviews.subtask-review', [
            'review'                => $review,
            'userCompletedSubtask'  => $userCompletedSubtask,
            'userCompletedTask'     => $userCompletedTask,
        ]);
    }

    /**
     * Mark a review as reviewed.
     *
     * @param ReviewUpdateRequest $request
     * @param int $reviewId
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function markReviewed(ReviewUpdateRequest $request, int $reviewId)
    {
        $data = $request->all();
        $critical = $request->get('critical', false);

        try {
            $review = $this->reviewRepository->find($reviewId);

            // If the review has pending tasks (not reviwed), than return back with error
            if ($this->reviewRepository->hasPendingTasks($review)) {
                return back()->withErrors('You can not approve a review that has pending tasks!');
            }

            if ($this->reviewRepository->hasOnlyApprovedTasks($review) && $critical ) {
                return back()->withErrors('You can not mark a review as having critical issues if all tasks are approved!');
            }

            DB::beginTransaction();
            $toBeMarkedAsApproved = false;
            $message = '';

            // If the review has declined tasks and from previous condition we deduced that there are no tasks with pending status, than mark review as declined.
            if ($this->reviewRepository->hasDeclinedTasks($review)) {
                $data['status'] = Review::STATUS_DECLINED;
                $data['completed_at'] = Carbon::now();

                // Mark review as declined.
                $this->reviewRepository->update($reviewId, $data);


                // Check if the current reviewed user has two reviews marked with critical for the current level and if yes, deactivate him
                $criticalReviews = $this->reviewRepository->model()
                    ->where('user_id', '=', $review->user_id)
                    ->where('critical', '=', Review::CRITICAL_YES)
                    ->where('user_level', '=', $review->user_level)
                    ->get();

                if (count($criticalReviews->toArray()) > 1) {
                    $this->userRepository->deactivate($review->userReviewed, [], auth()->user()->id);
                } else {
                    // Because we have declined the review, we need to make new records for user completed tasks and subtask so another review can be created
                    $userCompletedTaskCreateTransformer = new UserCompletedTaskCreateTransformer();
                    foreach ($review->userCompletedTasks as $userCompletedTask) {
                        $data = $userCompletedTaskCreateTransformer->transform($userCompletedTask);

                        // The user completed subtasks are created in the repo
                        $userCompletedTaskCreated = $this->userCompletedTaskRepository->create($data);
                    }
                }

                $toBeMarkedAsApproved = true;
                $message = 'Review of ' . $review->userReviewed->name . ' marked as declined.';
            }

            // If the review has only approved tasks and no pending or no canceled tasks than we mark review as approved
            if ($this->reviewRepository->hasOnlyApprovedTasks($review)) {
                $data['status'] = Review::STATUS_APPROVED;
                $data['completed_at'] = Carbon::now();

                // Mark review as approved.
                $this->reviewRepository->update($reviewId, $data);

                // Increase level for reviewed user
                $user = $review->userReviewed;

                $data = [];
                $data['level'] = $user->level + 1;
                $data['level_increased_at'] = Carbon::now();

                $user = $this->userRepository->update($user->id, $data);

                $toBeMarkedAsApproved = true;
                $message = 'Review of ' . $review->userReviewed->name . ' marked as approved.';
            }

            if ($toBeMarkedAsApproved) {
                // Mark reviewer's task as completed.
                $reviewSettingRepository = app()->make(ReviewSettingInterface::class);
                $reviewSetting = $reviewSettingRepository->model()->first();
                if (!$reviewSetting) {
                    return back()->withErrors('No review settings were found.');
                }
                if (!$reviewSetting->review_template_id) {
                    return back()->withErrors('Invalid review setting template_id.');
                }

                $subtaskReviewMarkedAsCompleted = false;
                $subtasksWithReviewModuleEnabled = 0;
                $subtaskRepository = app()->make(SubtaskInterface::class);
                $templateSubtaskModuleRepository = app()->make(TemplateSubtaskModuleInterface::class);

                foreach ($review->reviewerTask->subtasks as $subtask) {
                    $templateSubtaskModule = $templateSubtaskModuleRepository->model()
                        ->where("subtask_id", "=", $subtask->subtaskTemplateId)
                        ->first();

                    /// if the template subtask module record has review module assigned
                    if ($templateSubtaskModule && $templateSubtaskModule->subtask_module_id == Modules::MODULE_REVIEW_CHECK) {
                        $subtasksWithReviewModuleEnabled++;

                        if ( ! $subtask->user_id) {
                            $subtask->user_id = $subtask->task->user_id;
                        }
                        $subtask->completed_at = Carbon::now(); // Save the current time as the completed time
                        $subtask->save();
                        $subtaskReviewMarkedAsCompleted = true;

                        $activeSubtasksCount = $subtaskRepository->model()->where('task_id', $subtask->task->id)->whereNull('completed_at')->count();
                        if($activeSubtasksCount == 0) {
                            $taskRepository = app()->make(TaskInterface::class);
                            $taskRepository->markTaskAsCompleted($subtask->task);
                        }
                    }
                }

                if ($subtaskReviewMarkedAsCompleted === false) {
                    return back()->withErrors('No subtask with review module was found.');
                }

                if ($subtasksWithReviewModuleEnabled == 0) {
                    return back()->withErrors('There is no subtask with module review enabled for the reviewer task.');
                }

                if ($subtasksWithReviewModuleEnabled > 1) {
                    return back()->withErrors('More than one subtasks with module review enabled was found.');
                }

                DB::commit();
                return redirect()
                        ->action('ReviewsController@pending')
                        ->with('success', $message);
            } else {
                DB::rollback();
                return back()->withErrors('An error has occurred during the saving process of the review.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors($e->getMessage());
        }

        return back();
    }

    /**
     * Show the settings page for reviews.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function showSettings(Request $request)
    {
        $reviewSettingRepository = app()->make(ReviewSettingInterface::class);
        $reviewSetting = $reviewSettingRepository->model()->first();

        if (!$reviewSetting) {
            $reviewSetting = new ReviewSetting();
        }

        $templateRepository = app()->make(TemplateInterface::class);
        $templates = $templateRepository->all();

        $groupRepository = app()->make(GroupInterface::class);
        $groups = $groupRepository->all();

        $reviewTemplates = $reviewSetting->templates->pluck('template_id')->toArray();

        return view("reviews.show-settings", [
            'reviewSettings'    => $reviewSetting,
            'templates'         => $templates,
            'groups'            => $groups,
            'reviewTemplates'   => $reviewTemplates,
        ]);
    }

    /**
     * Save action for review settings.
     *
     * @param ReviewUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function saveSettings(ReviewUpdateRequest $request)
    {
        $data = $request->all();

        $reviewSettingRepository = app()->make(ReviewSettingInterface::class);
        // Use a custom update method in repository.
        // We are updating always the same record.
        $reviewSettingRepository->updateCustom($data);

        return back()->with('success', 'Settings have been saved.');

    }
}
