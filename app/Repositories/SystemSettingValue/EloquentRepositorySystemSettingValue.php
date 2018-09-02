<?php

namespace App\Repositories\SystemSettingValue;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositorySystemSettingValue extends BaseEloquentRepository implements SystemSettingValueInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositorySystemSettingValue constructor.
     *
     * @param SystemSettingValue $model
     */
    public function __construct(SystemSettingValue $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new SystemSettingValue.
     *
     * @param array $input
     * @return SystemSettingValue
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if (!$this->isValid('create', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }
        return $this->model->create($input);
    }

    /**
     * Update a SystemSettingValue.
     *
     * @param integer $id
     * @param array $input
     *
     * @return SystemSettingValue
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        if (!$this->isValid('update', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }

        $systemSettingValue = $this->find($id);
        if ($systemSettingValue) {
            $systemSettingValue->fill($input);
            $systemSettingValue->save();
            return $systemSettingValue;
        }

        throw new ModelNotFoundException(['Model SystemSettingValue not found.'], 404);

    }

    /**
     * Delete a SystemSettingValue.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $systemSettingValue = $this->model->find($id);
        if (!$systemSettingValue) {
            throw new HttpResponseException(response()->json(['Model SystemSettingValue not found.'], 404));
        }
        $systemSettingValue->delete();
    }
}