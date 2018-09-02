<?php
 
namespace App\Repositories\Flag;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryFlag extends BaseEloquentRepository implements FlagInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryFlag constructor.
     *
     * @param Flag $model
     */
    public function __construct(Flag $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Flag.
     *
     * @param array $input
     *
     * @return Flag
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
     * Update a Flag.
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

        $flag = $this->find($id);
        if ($flag) {
            $flag->fill($input);
            $flag->save();
            return $flag;
        }

        throw new ModelNotFoundException('Model Flag not found.', 404);
    }

    /**
     * Delete a Flag.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $flag = $this->model->find($id);
        if (!$flag) {
            throw new ModelNotFoundException('Model Flag not found.', 404);
        }
        $flag->delete();
    }
}