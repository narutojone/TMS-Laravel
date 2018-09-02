<?php
 
namespace App\Repositories\UserCompletedTask;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Review\Review;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtask;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtaskInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryUserCompletedTask extends BaseEloquentRepository implements UserCompletedTaskInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryUserCompletedTask constructor.
     *
     * @param UserCompletedTask $model
     */
    public function __construct(UserCompletedTask $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new userCompletedTask.
     *
     * @param array $input
     *
     * @return UserCompletedTask
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input) : UserCompletedTask
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        DB::beginTransaction();
        try {
            $userCompletedTask = $this->model->create($input);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // Create new user completed subtasks records
        $subtasks = $userCompletedTask->task->subtasks()->get();
        $userCompletedSubtaskRepo = app()->make(UserCompletedSubtaskInterface::class);

        foreach ($subtasks as $subtask) {
            try {
                $userCompletedSubtaskRepo->create([
                    'user_completed_task_id'    => $userCompletedTask->id,
                    'subtask_id'                => $subtask->id,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        }

        DB::commit();

        return $userCompletedTask;
    }

    /**
     * Update a userCompletedTask.
     *
     * @param integer $id
     * @param array $input
     *
     * @return UserCompletedTask
     * @throws ValidationException
     */
    public function update($id, array $input) : UserCompletedTask
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $userCompletedTask = $this->find($id);
        if ($userCompletedTask) {
            $userCompletedTask->fill($input);
            $userCompletedTask->save();
            return $userCompletedTask;
        }

        throw new ModelNotFoundException('Model UserCompletedTask not found', 404);
    }

    /**
     * Delete a userCompletedTask.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id) : void
    {
        $userCompletedTask = $this->model->find($id);
        $userCompletedTask->userCompletedSubtasks()->delete();

        if (!$userCompletedTask) {
            throw new ModelNotFoundException('Model UserCompletedTask not found', 404);
        }
        $userCompletedTask->delete();
    }

    /**
     * Return users due for automatic level increase (currently only for lvl 1 implemented)
     *
     * @param array $userLevels
     * @return Collection
     */
    public function getUsersForReview(array $userLevels = [1]) : Collection
    {
        $users = $this->model
            ->select('user_completed_tasks.user_id as userId', DB::raw('COUNT(DISTINCT user_completed_tasks.template_id) as tasksWithDifferentTemplatesCompleted'))
            ->whereIn('users.level', $userLevels)
            ->leftJoin('users', function ($join){
                $join->on('users.id', '=', 'user_completed_tasks.user_id');
            })
            ->groupBy('user_completed_tasks.user_id')
            ->get();

        return $users;
    }

    /**
     * Mark task review as approved, and also mark subtask reviews as approved if any.
     *
     * @param int $userCompletedTaskId
     * @param Review $review
     * @return UserCompletedTask
     * @throws ValidationException
     */
    public function markApproved(int $userCompletedTaskId, Review $review) : UserCompletedTask
    {
        $userCompletedSubtaskRepository = app()->make(UserCompletedSubtaskInterface::class);

        // Get user completed task model
        $userCompletedTask = $this->find($userCompletedTaskId);

        $data = $userCompletedTask->toArray();
        $data['status'] = UserCompletedTask::STATUS_APPROVED;

        // Mark task review as approved
        $userCompletedTask = $this->update($userCompletedTask->id, $data);

        // Approve all subtask reviews for the current task review if any.
        foreach ($userCompletedTask->userCompletedSubtasks as $userCompletedSubtask) {
            $data = $userCompletedSubtask->toArray();
            $data['status'] = UserCompletedSubtask::STATUS_APPROVED;

            $userCompletedSubtask = $userCompletedSubtaskRepository->update($userCompletedSubtask->id, $data);
        }

        return $userCompletedTask;
    }

    /**
     * Mark task review as declined, and also mark subtask reviews as declined if any.
     *
     * @param int $userCompletedTaskId
     * @param Review $review
     * @return UserCompletedTask
     * @throws ValidationException
     */
    public function markDeclined(int $userCompletedTaskId, Review $review) : UserCompletedTask
    {
        $userCompletedSubtaskRepository = app()->make(UserCompletedSubtaskInterface::class);

        // Get user completed task model
        $userCompletedTask = $this->find($userCompletedTaskId);

        $data = $userCompletedTask->toArray();
        $data['status'] = UserCompletedTask::STATUS_DECLINED;

        // Mark task review as approved
        $userCompletedTask = $this->update($userCompletedTask->id, $data);

        // Decline all subtask reviews for the current task review if any.
        foreach ($userCompletedTask->userCompletedSubtasks as $userCompletedSubtask) {
            $data = $userCompletedSubtask->toArray();
            $data['status'] = UserCompletedSubtask::STATUS_DECLINED;

            $userCompletedSubtask = $userCompletedSubtaskRepository->update($userCompletedSubtask->id, $data);
        }

        return $userCompletedTask;
    }
}