<?php
 
namespace App\Repositories\UserCustomerType;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\UserCustomerType\UserCustomerType;
use App\Repositories\UserCustomerType\UserCustomerTypeInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryUserCustomerType extends BaseEloquentRepository implements UserCustomerTypeInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryUserCustomerType constructor.
	 *
	 * @param App\Respositories\UserCustomerType\UserCustomerType $model
	 */
	public function __construct(UserCustomerType $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new UserCustomerType.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserCustomerType\UserCustomerType
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a UserCustomerType.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\UserCustomerType\UserCustomerType
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$userCustomerType = $this->find($input['id']);
        if ($userCustomerType) {
            $userCustomerType->fill($input);
            $userCustomerType->save();
            return $userCustomerType;
		}
		
		throw new HttpResponseException(response()->json(['Model UserCustomerType not found.'], 404));
	}
 
	/**
	 * Delete a UserCustomerType.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$userCustomerType = $this->model->find($id);
		if (!$userCustomerType) {
			throw new HttpResponseException(response()->json(['Model UserCustomerType not found.'], 404));
		}
		$userCustomerType->delete();
	}
}