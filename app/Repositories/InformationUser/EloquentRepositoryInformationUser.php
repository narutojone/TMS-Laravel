<?php
 
namespace App\Repositories\InformationUser;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryInformationUser extends BaseEloquentRepository implements InformationUserInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryInformationUser constructor.
     *
     * @param InformationUser $model
     */
    public function __construct(InformationUser $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new InformationUser.
     *
     * @param array $input
     *
     * @return InformationUser
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
     * Update a InformationUser.
     *
     * @param integer $id
     * @param array $input
     *
     * @return InformationUser
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $informationUser = $this->find($id);
        if ($informationUser) {
            $informationUser->fill($input);
            $informationUser->save();
            return $informationUser;
        }

        throw new ModelNotFoundException('Model InformationUser not found', 404);
    }
 
    /**
     * Delete a InformationUser.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $informationUser = $this->model->find($id);
        if (!$informationUser) {
            throw new ModelNotFoundException('Model InformationUser not found', 404);
        }
        $informationUser->delete();
    }
}