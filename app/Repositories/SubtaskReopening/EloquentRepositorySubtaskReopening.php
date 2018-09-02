<?php
 
namespace App\Repositories\SubtaskReopening;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositorySubtaskReopening extends BaseEloquentRepository implements SubtaskReopeningInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositorySubtaskReopening constructor.
     *
     * @param SubtaskReopening $model
     */
    public function __construct(SubtaskReopening $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new SubtaskReopening.
     *
     * @param array $input
     *
     * @return SubtaskReopening
     * @throws ValidationException
     */
    public function create(array $input) : SubtaskReopening
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    /**
     * Update a SubtaskReopening.
     *
     * @param integer $id
     * @param array $input
     *
     * @return SubtaskReopening
     * @throws ValidationException
     */
    public function update($id, array $input) : SubtaskReopening
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $subtaskReopening = $this->find($input['id']);
        if ($subtaskReopening) {
            $subtaskReopening->fill($input);
            $subtaskReopening->save();
            return $subtaskReopening;
        }

        throw new ModelNotFoundException('Model SubtaskReopening not found', 404);
    }

    /**
     * Delete a SubtaskReopening.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $subtaskReopening = $this->model->find($id);

        if (!$subtaskReopening) {
            throw new ModelNotFoundException('Model SubtaskReopening not found', 404);
        }

        $subtaskReopening->delete();
    }
}