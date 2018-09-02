<?php
 
namespace App\Repositories\TaskOverdueReason;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryTaskOverdueReason extends BaseEloquentRepository implements TaskOverdueReasonInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplate constructor.
     *
     * @param TaskOverdueReason $model
     */
    public function __construct(TaskOverdueReason $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TaskOverdueReason.
     *
     * @param array $input
     *
     * @return TaskOverdueReason
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $taskOverdueReason = $this->model->create($input);
        return $taskOverdueReason;
    }

    /**
     * Update a TaskOverdueReason.
     *
     * @param integer $id
     * @param array $input
     *
     * @return TaskOverdueReason
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $taskOverdueReason = $this->find($id);
        if ($taskOverdueReason) {
            $taskOverdueReason->fill($input);
            $taskOverdueReason->save();
            return $taskOverdueReason;
        }

        throw new ModelNotFoundException('Model TaskOverdueReason not found', 404);
    }


    /**
	 * Delete a TaskOverdueReason.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
        $taskOverdueReason = $this->model->find($id);
        if (!$taskOverdueReason) {
            throw new ModelNotFoundException('Model TaskOverdueReason not found.', 404);
        }

        $taskOverdueReason->delete();
	}

    /**
     * Prepare data for db insert
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        return $input;
    }

    /**
     * Prepare data for db update
     *
     * @param array $input
     * @return array
     */
    protected function prepareUpdateData(array $input)
    {
        return $input;
    }



}