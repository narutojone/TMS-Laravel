<?php
 
namespace App\Repositories\UserCompletedSubtask;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Review\Review;
use App\Repositories\Task\TaskInterface;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use App\Repositories\UserCompletedTask\UserCompletedTaskInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryUserCompletedSubtask extends BaseEloquentRepository implements UserCompletedSubtaskInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryUserCompletedSubtask constructor.
     *
     * @param UserCompletedSubtask $model
     */
    public function __construct(UserCompletedSubtask $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new userCompletedSubtask.
     *
     * @param array $input
     *
     * @return UserCompletedSubtask
     * @throws ValidationException
     */
    public function create(array $input) : UserCompletedSubtask
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a userCompletedSubtask.
     *
     * @param integer $id
     * @param array $input
     *
     * @return UserCompletedSubtask
     * @throws ValidationException
     */
    public function update($id, array $input) : UserCompletedSubtask
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $userCompletedSubtask = $this->find($id);
        if ($userCompletedSubtask) {
            $userCompletedSubtask->fill($input);
            $userCompletedSubtask->save();
            return $userCompletedSubtask;
        }

        throw new ModelNotFoundException('Model UserCompletedSubtask not found', 404);
    }

    /**
     * Delete a userCompletedSubtask.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id) : void
    {
        $userCompletedSubtask = $this->model->find($id);
        if (!$userCompletedSubtask) {
            throw new ModelNotFoundException('Model UserCompletedSubtask not found', 404);
        }
        $userCompletedSubtask->delete();
    }

    /**
     * Mark a subtask as approved.
     *
     * @param int $userCompletedSubtaskId
     * @param Review $review
     * @return UserCompletedSubtask
     * @throws ValidationException
     */
    public function markApproved(int $userCompletedSubtaskId, Review $review) : UserCompletedSubtask
    {
        // Get user completed task model
        $userCompletedSubtask = $this->find($userCompletedSubtaskId);

        // Update status for current subtask review
        $data = $userCompletedSubtask->toArray();
        $data['status'] = UserCompletedSubtask::STATUS_APPROVED;
        $userCompletedSubtask = $this->update($userCompletedSubtaskId, $data);

        // Check if we need to update the status for the current user completed task review
        $userCompletedTask = $this->checkCurrentUserCompletedTask($userCompletedSubtask);

        return $userCompletedSubtask;
    }

    /**
     * Check if the user completed task needs to have it's status updated automatically
     *
     * @param UserCompletedSubtask $userCompletedSubtask
     * @return UserCompletedTask
     */
    public function checkCurrentUserCompletedTask(UserCompletedSubtask $userCompletedSubtask) : ?UserCompletedTask
    {
        $initialUserCompletedSubtask = $userCompletedSubtask;
        $userCompletedTask = null;

        $hasPending = 0;
        $hasDeclined = 0;
        $hasApproved = 0;

        $userCompletedTaskId = $userCompletedSubtask->user_completed_task_id;
        $userCompletedSubtasks = $this->model->where('user_completed_task_id', $userCompletedTaskId)->get();

        if (empty($userCompletedSubtasks)) {
            throw new ModelNotFoundException('Subtasks reviews not found', 404);
        }

        foreach ($userCompletedSubtasks->toArray() as $userCompletedSubtask) {
            if ($userCompletedSubtask['status'] == UserCompletedSubtask::STATUS_APPROVED) {
                $hasApproved++;
            } elseif ($userCompletedSubtask['status'] == UserCompletedSubtask::STATUS_PENDING) {
                $hasPending++;
            } elseif ($userCompletedSubtask['status'] == UserCompletedSubtask::STATUS_DECLINED) {
                $hasDeclined++;
            }
        }


        // Automatically update the status for the user task completed if we have no more pending user completed subtasks
        if ($hasPending == 0) { // all subtask are reviewed
            $userCompletedTaskRepository = app()->make(UserCompletedTaskInterface::class);
            $data = [];

            if ($hasDeclined > 0) { // at least one subtask was declined
                // Mark task review as declined
                $data['status'] = UserCompletedTask::STATUS_DECLINED;

                // Reopen task, but with no subtasks ids added to the reopenData, because, each subtask gets a reopening when it is individually declined.
                $reopenData = [];
                $reopenData['reason'] = 'Review: Reopened during review, see subtasks for details.';
                $taskRepository = app()->make(TaskInterface::class);
                $taskRepository->reopen($initialUserCompletedSubtask->userCompletedTask->task, auth()->user()->id, $reopenData);

            } else { // there are only approved subtasks for the current user completed task
                $data['status'] = UserCompletedTask::STATUS_APPROVED;
            }
            $userCompletedTask = $userCompletedTaskRepository->update($userCompletedTaskId, $data);
        }

        return $userCompletedTask;
    }

    public function markDeclined(int $userCompletedSubtaskId, Review $review)
    {
        // Get user completed task model
        $userCompletedSubtask = $this->find($userCompletedSubtaskId);

        // Update status for current subtask review
        $data = $userCompletedSubtask->toArray();
        $data['status'] = UserCompletedSubtask::STATUS_DECLINED;
        $userCompletedSubtask = $this->update($userCompletedSubtaskId, $data);

        // Check if we need to update the status for the current user completed task review
        $userCompletedTask = $this->checkCurrentUserCompletedTask($userCompletedSubtask);

        return $userCompletedSubtask;
    }
}