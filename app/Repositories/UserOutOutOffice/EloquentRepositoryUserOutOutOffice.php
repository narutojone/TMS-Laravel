<?php
 
namespace App\Repositories\UserOutOutOffice;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryUserOutOutOffice extends BaseEloquentRepository implements UserOutOutOfficeInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryUserOutOutOffice constructor.
	 *
	 * @param UserOutOutOffice $model
	 */
	public function __construct(UserOutOutOffice $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}

    /**
     * Create a new UserOutOutOffice.
     *
     * @param array $input
     *
     * @return UserOutOutOffice
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
     * Update a UserOutOutOffice.
     *
     * @param integer $id
     * @param array $input
     *
     * @return UserOutOutOffice
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
	public function update($id, array $input)
	{
		if(!$this->isValid('update', $input)) {
			throw new ValidationException($this->validators['update']);
		}
		
		$userOutOutOffice = $this->find($id);
        if ($userOutOutOffice) {
            $userOutOutOffice->fill($input);
            $userOutOutOffice->save();
            return $userOutOutOffice;
		}
		
		throw new ModelNotFoundException('Model UserOutOutOffice not found', 404);
	}
 
	/**
	 * Delete a UserOutOutOffice.
	 *
	 * @param integer $id
	 *
	 * @return boolean
     * @throws ModelNotFoundException
	 */
	public function delete($id)
	{
		$userOutOutOffice = $this->model->find($id);
		if (!$userOutOutOffice) {
			throw new ModelNotFoundException('Model UserOutOutOffice not found', 404);
		}
		$userOutOutOffice->delete();
	}
}