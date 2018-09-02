<?php
 
namespace App\Repositories\HarvestDevTimeEntry;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryHarvestDevTimeEntry extends BaseEloquentRepository implements HarvestDevTimeEntryInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryHarvestDevTimeEntry constructor.
     * @param HarvestDevTimeEntry $model
     */
    public function __construct(HarvestDevTimeEntry $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new HarvestDevTimeEntry.
     *
     * @param array $input
     *
     * @return HarvestDevTimeEntry
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $task = $this->model->create($input);

        return $task;
    }

    /**
     * Update a HarvestDevTimeEntry.
     *
     * @param integer $id
     * @param array $input
     *
     * @return HarvestDevTimeEntry
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $harvestDevTimeEntry = $this->find($id);
        if ($harvestDevTimeEntry) {
            $harvestDevTimeEntry->fill($input);
            $harvestDevTimeEntry->save();
            return $harvestDevTimeEntry;
        }
        throw new ModelNotFoundException('Model HarvestDevTimeEntry not found', 404);
    }

    /**
     * Delete a HarvestDevTimeEntry.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $harvestDevTimeEntry = $this->model->find($id);
        if (!$harvestDevTimeEntry) {
            throw new HttpResponseException(response()->json(['Model HarvestDevTimeEntry not found.'], 404));
        }
        $harvestDevTimeEntry->delete();
    }
}