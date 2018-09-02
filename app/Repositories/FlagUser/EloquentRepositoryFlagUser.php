<?php
 
namespace App\Repositories\FlagUser;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryFlagUser extends BaseEloquentRepository implements FlagUserInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryFlagUser constructor.
     *
     * @param FlagUser $model
     */
    public function __construct(FlagUser $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new FlagUser.
     *
     * @param array $input
     *
     * @return FlagUser
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
     * Update a FlagUser.
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

        $flagUser = $this->find($id);
        if ($flagUser) {
            $flagUser->fill($input);
            $flagUser->save();
            return $flagUser;
        }

        throw new ModelNotFoundException('Model FlagUser not found.', 404);
    }

    /**
     * Delete a FlagUser.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $flagUser = $this->model->find($id);
        if (!$flagUser) {
            throw new ModelNotFoundException('Model FlagUser not found.', 404);
        }
        $flagUser->delete();
    }
}