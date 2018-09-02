<?php
 
namespace App\Repositories\System;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositorySystem extends BaseEloquentRepository implements SystemInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositorySystem constructor.
     *
     * @param System $model
     */
    public function __construct(System $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new System.
     *
     * @param array $input
     *
     * @return System
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
     * Update a System.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $system = $this->find($id);
        if ($system) {
            $system->fill($input);
            $system->save();
            return $system;
        }

        throw new ModelNotFoundException('Model System not found', 404);
    }

    /**
     * Delete a System.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $system = $this->model->find($id);
        if (!$system) {
            throw new ModelNotFoundException('Model System not found', 404);
        }
        $system->delete();
    }
}