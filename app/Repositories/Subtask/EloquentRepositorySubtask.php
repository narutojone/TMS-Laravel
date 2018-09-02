<?php
 
namespace App\Repositories\Subtask;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Repositories\SubtaskReopening\SubtaskReopeningInterface;
use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\DB;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositorySubtask extends BaseEloquentRepository implements SubtaskInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositorySubtask constructor.
     *
     * @param Subtask $model
     */
    public function __construct(Subtask $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Subtask.
     *
     * @param array $input
     *
     * @return Subtask
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    /**
     * Update a Subtask.
     *
     * @param integer $id
     * @param array $input
     *
     * @return Subtask
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $subtask = $this->find($id);
        if ($subtask) {
            $subtask->fill($input);
            $subtask->save();
            return $subtask;
        }

        throw new ModelNotFoundException('Model Subtask not found.', 404);
    }

    /**
     * Delete a Subtask.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $subtask = $this->model->find($id);
        if (!$subtask) {
            throw new ModelNotFoundException('Model Subtask not found.', 404);
        }
        $subtask->delete();
    }

    /**
     * Create reopenings for a subtask
     *
     * @param int $subtaskId - the id of the Subtask for whom we are doing the reopening
     * @param int $authenticatedUserId - user who is performing the action of reopening
     * @param array $input - data received via request form
     *
     * @return Subtask
     * @throws ValidationException
     */
    public function createReopenings(int $subtaskId, int $authenticatedUserId, array $input)
    {
        $subtask = $this->find($subtaskId);
        $completedAt = $subtask->completed_at; // initial datetime when the subtask was completed

        // check if user is changed
        $userRepository = app()->make(UserInterface::class);

        $dataToUpdate = [];
        $dataToUpdate = ['completed_at'  => NULL];
        if (isset($input['user']) && $input['user']) {
            $user = $userRepository->find($input['user']);
            $userId = ($subtask->template && $user) ? $user->canProcessTemplate($subtask->template->template) ? $user->id : null : $authenticatedUserId;
            if ($userId) {
                $dataToUpdate['user_id'] = $userId;
            }
        }

        DB::beginTransaction();

        // Set the subtask as not completed
        $subtask = $this->update($subtaskId, $dataToUpdate);

        // Create an entry for the subtask reopening
        $subtaskReopeningRepository = app()->make(SubtaskReopeningInterface::class);
        $subtaskReopening = $subtaskReopeningRepository->create([
            'user_id'       => $authenticatedUserId,
            'reason'        => $input['reason'] . ' (Assigned User: ' . $subtask->user->name . ')',
            'completed_at'  => $completedAt,
            'subtask_id'    => $subtaskId,
        ]);

        DB::commit();

        return $subtask;
    }
}