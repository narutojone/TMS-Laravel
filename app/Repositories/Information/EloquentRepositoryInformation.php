<?php
 
namespace App\Repositories\Information;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryInformation extends BaseEloquentRepository implements InformationInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryInformation constructor.
     *
     * @param Information $model
     */
    public function __construct(Information $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Information.
     *
     * @param array $input
     *
     * @return Information
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
     * Update a Information.
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

        $information = $this->find($id);
        if ($information) {
            $information->fill($input);
            $information->save();
            return $information;
        }

        throw new ModelNotFoundException('Model Information not found', 404);
    }
 
    /**
     * Delete a Information.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $information = $this->model->find($id);
        if (!$information) {
            throw new ModelNotFoundException('Model Information not found', 404);
        }
        $information->delete();
    }
}