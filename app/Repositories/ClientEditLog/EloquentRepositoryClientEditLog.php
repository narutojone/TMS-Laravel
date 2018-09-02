<?php

namespace App\Repositories\ClientEditLog;

use App\Repositories\BaseEloquentRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class EloquentRepositoryClientEditLog extends BaseEloquentRepository implements ClientEditLogInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryClientEditLog constructor.
     * @param ClientEditLog $model
     */
    public function __construct(ClientEditLog $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * @param array $input
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
     * Update ClientEditLog.
     *
     * @param integer $id
     * @param array $input
     *
     * @return ClientEditLog
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $editLog = $this->find($id);
        
        $editLog->fill($input);
        $editLog->save();
        
        return $editLog;
    }

    /**
     * Set the end date of the last change log of the field
     *
     * @param $clientId
     * @param $field
     */
    public function endLatest($clientId, $field)
    {
        $this->model()
            ->where('client_id', $clientId)
            ->where('field', $field)
            ->where('ends_at', null)
            ->update([
                'ends_at' => Carbon::now(),
            ]);
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

    /**
     * Prepare data for update.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data) : array
    {
        return $data;
    }
}
