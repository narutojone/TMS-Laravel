<?php
 
namespace App\Repositories\TaskType;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\TaskType\TaskType;
use App\Repositories\TaskType\TaskTypeInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTaskType extends BaseEloquentRepository implements TaskTypeInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryTaskType constructor.
	 *
	 * @param App\Respositories\TaskType\TaskType $model
	 */
	public function __construct(TaskType $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new TaskType.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\TaskType\TaskType
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a TaskType.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\TaskType\TaskType
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$taskType = $this->find($input['id']);
        if ($taskType) {
            $taskType->fill($input);
            $taskType->save();
            return $taskType;
		}
		
		throw new HttpResponseException(response()->json(['Model TaskType not found.'], 404));
	}
 
	/**
	 * Delete a TaskType.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$taskType = $this->model->find($id);
		if (!$taskType) {
			throw new HttpResponseException(response()->json(['Model TaskType not found.'], 404));
		}
		$taskType->delete();
	}
}