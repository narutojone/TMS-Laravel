<?php

namespace App\Repositories\Option;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Template\Template;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryOption extends BaseEloquentRepository implements OptionInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * @var Template $template
     */
    protected $template = null;

    /**
     * EloquentRepositoryOption constructor.
     *
     * @param Option $model
     */
    public function __construct(Option $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Option.
     *
     * @param array $input
     *
     * @return Option
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $task = $this->model->create($input);

        return $task;
    }

    /**
     * Update a Option.
     *
     * @param integer $id
     * @param array $input
     *
     * @return Option
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $option = $this->find($id);
        if ($option) {
            $option->fill($input);
            $option->save();
            return $option;
        }

        throw new ModelNotFoundException('Model Option not found', 404);
    }

    /**
     * Delete a Option.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        // An option can't be deleted by an user.
        // You need to create a migration and remove references from the code.
        throw new ModelNotFoundException('Model Option not found', 404);

        // This piece of code is not used at the moment.
        // It's left here in case we'll change the deletion logic in the future.
        $option = $this->model->find($id);
        if (!$option) {
            throw new ModelNotFoundException('Model Option not found', 404);
        }

        $option->delete();
    }

    protected function prepareCreateData(array $input)
    {
        return $input;
    }

    private function prepareUpdateData($id, $input)
    {
        return $input;
    }

}