<?php
 
namespace App\Repositories\TaskReopening;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTaskReopening extends BaseEloquentRepository implements TaskReopeningInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTaskReopening constructor.
     *
     * @param TaskReopening $model
     */
    public function __construct(TaskReopening $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TaskReopening.
     *
     * @param array $input
     *
     * @return TaskReopening
     * @throws ValidationException
     */
    public function create(array $input) : TaskReopening
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a TaskReopening.
     *
     * @param integer $id
     * @param array $input
     *
     * @return TaskReopening
     * @throws ValidationException
     */
    public function update($id, array $input) : TaskReopening
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $taskReopening = $this->find($id);
        if ($taskReopening) {
            $taskReopening->fill($input);
            $taskReopening->save();
            return $taskReopening;
        }

        throw new ModelNotFoundException('Model TaskReopening not found', 404);
    }

    /**
     * Delete a TaskReopening.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $taskReopening = $this->model->find($id);
        if (!$taskReopening) {
            throw new ModelNotFoundException('Model TaskReopening not found', 404);
        }
        $taskReopening->delete();
    }
}