<?php

namespace App\Repositories\ZendeskGroup;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryZendeskGroup extends BaseEloquentRepository implements ZendeskGroupInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryZendeskGroup constructor.
     *
     * @param ZendeskGroup $model
     */
    public function __construct(ZendeskGroup $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new ZenDesk Group.
     *
     * @param array $input
     * @return ZendeskGroup
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $zendeskGroup = $this->model->create($input);

        return $zendeskGroup;
    }

    /**
     * Update a ZenDesk Group.
     *
     * @param integer $id
     * @param array $input
     * @return ZendeskGroup
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $zendeskGroup = $this->find($id);

        if ($zendeskGroup) {
            $zendeskGroup->fill($input);
            $zendeskGroup->save();
            return $zendeskGroup;
        }

        throw new ModelNotFoundException('Model ZendeskGroup not found.', 404);
    }

    /**
     * Prepare data for update action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data): array
    {
        return $data;
    }

    /**
     * Prepare data for create.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCreateData(array $data): array
    {
        if(!isset($data['deleted'])) {
            $data['deleted'] = 0;
        }
        return $data;
    }

}