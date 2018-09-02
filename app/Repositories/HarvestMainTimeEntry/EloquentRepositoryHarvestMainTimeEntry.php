<?php

namespace App\Repositories\HarvestMainTimeEntry;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryHarvestMainTimeEntry extends BaseEloquentRepository implements HarvestMainTimeEntryInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGithubIssue.php constructor.
     *
     * @param HarvestMainTimeEntry $model
     */
    public function __construct(HarvestMainTimeEntry $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new HarvestMainTimeEntry.
     *
     * @param array $input
     *
     * @return HarvestMainTimeEntry
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $task = $this->model->create($input);

        return $task;
    }

    /**
     * Update a HarvestMainTimeEntry.
     *
     * @param integer $id
     * @param array $input
     *
     * @return HarvestMainTimeEntry
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $HarvestMainTimeEntry = $this->find($id);
        if ($HarvestMainTimeEntry) {
            $HarvestMainTimeEntry->fill($input);
            $HarvestMainTimeEntry->save();
            return $HarvestMainTimeEntry;
        }
        throw new ModelNotFoundException('Model HarvestMainTimeEntry not found', 404);
    }

    /**
     * Delete a HarvestMainTimeEntry.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $harvestMainTimeEntry = $this->model->find($id);
        if (!$harvestMainTimeEntry) {
            throw new HttpResponseException(response()->json(['Model HarvestMainTimeEntry not found.'], 404));
        }
        $harvestMainTimeEntry->delete();
    }
}