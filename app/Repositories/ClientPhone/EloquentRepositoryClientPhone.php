<?php
 
namespace App\Repositories\ClientPhone;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\ClientPhone\ClientPhone;
use App\Repositories\ClientPhone\ClientPhoneInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryClientPhone extends BaseEloquentRepository implements ClientPhoneInterface
{
	/**
	 * @var $model
	 */
	protected $model;

    /**
     * EloquentRepositoryClientPhone constructor.
     *
     * @param ClientPhone $model
     */
    public function __construct(ClientPhone $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new client phone.
     *
     * @param array $input
     *
     * @return ClientPhone
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
     * Update a client phone.
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

        $clientPhone = $this->find($id);
        if ($clientPhone) {
            $clientPhone->fill($input);
            $clientPhone->save();
            return $clientPhone;
        }

        throw new ModelNotFoundException('Model ClientPhone not found.', 404);
    }

    /**
     * Delete a client phone.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $clientPhone = $this->model->find($id);
        if (!$clientPhone) {
            throw new ModelNotFoundException('Model ClientPhone not found.', 404);
        }
        $clientPhone->delete();
    }

    /**
     * Prepare data for update action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data) : array
    {
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
        return $data;
    }

}