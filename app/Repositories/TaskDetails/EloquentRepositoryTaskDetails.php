<?php

namespace App\Repositories\TaskDetails;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryTaskDetails extends BaseEloquentRepository implements TaskDetailsInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTaskDetails constructor.
     *
     * @param \App\Repositories\TaskDetails\TaskDetails $model
     */
    public function __construct(TaskDetails $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TaskDetails.
     *
     * @param array $input
     *
     * @return TaskDetails
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a TaskDetails.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $taskDetails = $this->find($id);
        if ($taskDetails) {
            $taskDetails->fill($input);
            $taskDetails->save();
            return $taskDetails;
        }

        throw new ModelNotFoundException('Model TaskDetails not found.', 404);
    }

    /**
     * Delete a TaskDetails.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $taskDetails = $this->model->find($id);
        if (!$taskDetails) {
            throw new ModelNotFoundException('Model TaskDetails not found.', 404);
        }
        $taskDetails->delete();
    }

    /**
     * Prepare data for update action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data) : array
    {
        if(array_key_exists('description', $data) && is_null($data['description'])) {
            $data['description'] = '';
        }
        return $data;
    }

    /**
     * Prepare data for create.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCreateData(array $data) : array
    {
        if(!isset($data['description']) || is_null($data['description'])) {
            $data['description'] = '';
        }
        return $data;
    }
}