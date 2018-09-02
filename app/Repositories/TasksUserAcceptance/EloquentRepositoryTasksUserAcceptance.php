<?php
 
namespace App\Repositories\TasksUserAcceptance;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptance;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTasksUserAcceptance extends BaseEloquentRepository implements TasksUserAcceptanceInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTasksUserAcceptance constructor.
     *
     * @param TasksUserAcceptance $model
     */
    public function __construct(TasksUserAcceptance $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new TasksUserAcceptance.
     *
     * @param array $input
     * @return TasksUserAcceptance
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
     * Update a TasksUserAcceptance.
     *
     * @param integer $id
     * @param array $input
     * @return TasksUserAcceptance
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }
        
        $tasksUserAcceptance = $this->find($id);
        if ($tasksUserAcceptance) {
            $tasksUserAcceptance->fill($input);
            $tasksUserAcceptance->save();
            return $tasksUserAcceptance;
        }
        
        throw new ModelNotFoundException('Model TasksUserAcceptance not found', 404);
    }
 
    /**
     * Delete a TasksUserAcceptance.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $tasksUserAcceptance = $this->model->find($id);
        if (!$tasksUserAcceptance) {
            throw new ModelNotFoundException('Model TasksUserAcceptance not found', 404);
        }
        $tasksUserAcceptance->delete();
    }
}