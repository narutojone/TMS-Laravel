<?php
 
namespace App\Repositories\UserSystem;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\UserSystem\UserSystem;
use App\Repositories\UserSystem\UserSystemInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryUserSystem extends BaseEloquentRepository implements UserSystemInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryUserSystem constructor.
	 *
	 * @param App\Respositories\UserSystem\UserSystem $model
	 */
	public function __construct(UserSystem $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new UserSystem.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserSystem\UserSystem
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a UserSystem.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserSystem\UserSystem
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$userSystem = $this->find($input['id']);
        if ($userSystem) {
            $userSystem->fill($input);
            $userSystem->save();
            return $userSystem;
		}
		
		throw new HttpResponseException(response()->json(['Model UserSystem not found.'], 404));
	}
 
	/**
	 * Delete a UserSystem.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$userSystem = $this->model->find($id);
		if (!$userSystem) {
			throw new HttpResponseException(response()->json(['Model UserSystem not found.'], 404));
		}
		$userSystem->delete();
	}
}