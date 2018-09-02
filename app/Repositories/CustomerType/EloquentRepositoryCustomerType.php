<?php
 
namespace App\Repositories\CustomerType;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\CustomerType\CustomerType;
use App\Repositories\CustomerType\CustomerTypeInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryCustomerType extends BaseEloquentRepository implements CustomerTypeInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryCustomerType constructor.
	 *
	 * @param App\Respositories\CustomerType\CustomerType $model
	 */
	public function __construct(CustomerType $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new CustomerType.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\CustomerType\CustomerType
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a CustomerType.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\CustomerType\CustomerType
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$customerType = $this->find($input['id']);
        if ($customerType) {
            $customerType->fill($input);
            $customerType->save();
            return $customerType;
		}
		
		throw new HttpResponseException(response()->json(['Model CustomerType not found.'], 404));
	}
 
	/**
	 * Delete a CustomerType.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$customerType = $this->model->find($id);
		if (!$CustomerType) {
			throw new HttpResponseException(response()->json(['Model CustomerType not found.'], 404));
		}
		$customerType->delete();
	}
}