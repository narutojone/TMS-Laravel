<?php
 
namespace App\Repositories\UserTaskType;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\UserTaskType\UserTaskType;
use App\Repositories\UserTaskType\UserTaskTypeInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryUserTaskType extends BaseEloquentRepository implements UserTaskTypeInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryUserTaskType constructor.
	 *
	 * @param App\Respositories\UserTaskType\UserTaskType $model
	 */
	public function __construct(UserTaskType $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new UserTaskType.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserTaskType\UserTaskType
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a UserTaskType.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserTaskType\UserTaskType
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$userTaskType = $this->find($input['id']);
        if ($userTaskType) {
            $userTaskType->fill($input);
            $userTaskType->save();
            return $userTaskType;
		}
		
		throw new HttpResponseException(response()->json(['Model UserTaskType not found.'], 404));
	}
 
	/**
	 * Delete a UserTaskType.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$userTaskType = $this->model->find($id);
		if (!$userTaskType) {
			throw new HttpResponseException(response()->json(['Model UserTaskType not found.'], 404));
		}
		$userTaskType->delete();
	}
}