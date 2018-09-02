<?php
 
namespace App\Repositories\GroupUser;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGroupUser extends BaseEloquentRepository implements GroupUserInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGroupUser constructor.
     *
     * @param GroupUser $model
     */
    public function __construct(GroupUser $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new GroupUser.
     *
     * @param array $input
     *
     * @return GroupUser
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
     * Update a GroupUser.
     *
     * @param integer $id
     * @param array $input
     *
     * @return GroupUser
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }
        
        $groupUser = $this->find($id);
        if ($groupUser) {
            $groupUser->fill($input);
            $groupUser->save();
            return $groupUser;
        }
        
        throw new ModelNotFoundException('Model GroupUser not found', 404);
    }

    /**
     * Delete a GroupUser.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $groupUser = $this->model->find($id);
        if (!$groupUser) {
            throw new ModelNotFoundException('Model GroupUser not found', 404);
        }
        $groupUser->delete();
    }
}