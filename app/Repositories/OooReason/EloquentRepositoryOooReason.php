<?php
 
namespace App\Repositories\OooReason;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryOooReason extends BaseEloquentRepository implements OooReasonInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryOooReason constructor.
     *
     * @param OooReason $model
     */
    public function __construct(OooReason $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new OooReason.
     *
     * @param array $input
     *
     * @return OooReason
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        if ($input['default'] == OooReason::DEFAULT) {
            $this->resetDefaultStatus();
        }

        return $this->model->create($input);
    }

    /**
     * Update a OooReason.
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

        if ($input['default'] == OooReason::DEFAULT) {
            $this->resetDefaultStatus();
        }

        $oooReason = $this->find($id);
        if ($oooReason) {
            $oooReason->fill($input);
            $oooReason->save();
            return $oooReason;
        }

        throw new ModelNotFoundException('Model OooReason not found.', 404);
    }

    /**
     * Delete a OooReason.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $oooReason = $this->model->find($id);
        if (!$oooReason) {
            throw new ModelNotFoundException('Model OooReason not found.', 404);
        }

        $oooReason->delete();
    }

    /**
     * Prepare data for update operation
     *
     * @param array $input
     * @return array
     */
    protected function prepareUpdateData(array $input)
    {
        if (!isset($input['default'])) {
            $input['default'] = OooReason::NOT_DEFAULT;
        }

        return $input;
    }

    /**
     * Prepare data for create action
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        if (!isset($input['default'])) {
            $input['default'] = OooReason::NOT_DEFAULT;
        }

        return $input;
    }

    /**
     * Update all records to eliminate any default reason status
     *
     * @return bool
     */
    public function resetDefaultStatus()
    {
        return $this->model
                ->where('default', OooReason::DEFAULT)
                ->update(['default' => OooReason::NOT_DEFAULT]);
    }
}