<?php
 
namespace App\Repositories\Example;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Example\Example;
use App\Repositories\Example\ExampleInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryExample extends BaseEloquentRepository implements ExampleInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryExample constructor.
	 *
	 * @param App\Respositories\Example\Example $model
	 */
	public function __construct(Example $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new example.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\Example\Example
	 */
	public function create(array $attributes)
	{
		$input = $this->prepareCreateData($attributes);
		
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a example.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\Example\Example
	 */
	public function update($id, array $attributes)
	{
		$input = $this->prepareUpdateData($id, $attributes);

		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$example = $this->find($input['id']);
        if ($example) {
            $example->fill($input);
            $example->save();
            return $example;
		}
		
		throw new HttpResponseException(response()->json(['Model Example not found.'], 404));
	}
 
	/**
	 * Delete a example.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$example = $this->model->find($id);
		if (!$example) {
			throw new HttpResponseException(response()->json(['Model Example not found.'], 404));
		}
		$example->delete();
	}

	private function prepareCreateData($input)
	{
		$input['some_json'] = json_encode($input['some_json']);
		return $input;
	}

	private function prepareUpdateData($id, $input)
	{
		$input['id'] = $id;
		$input['some_json'] = json_encode($input['some_json']);
		return $input;
	}
}