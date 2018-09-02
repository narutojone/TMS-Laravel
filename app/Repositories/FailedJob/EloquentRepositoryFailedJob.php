<?php
 
namespace App\Repositories\FailedJob;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\FailedJob\FailedJob;
use App\Repositories\FailedJob\FailedJobInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryFailedJob extends BaseEloquentRepository implements FailedJobInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryFailedJob constructor.
	 *
	 * @param App\Respositories\FailedJob\FailedJob $model
	 */
	public function __construct(FailedJob $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new FailedJob.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\FailedJob\FailedJob
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a FailedJob.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\FailedJob\FailedJob
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$failedJob = $this->find($input['id']);
        if ($failedJob) {
            $failedJob->fill($input);
            $failedJob->save();
            return $failedJob;
		}
		
		throw new HttpResponseException(response()->json(['Model FailedJob not found.'], 404));
	}
 
	/**
	 * Delete a FailedJob.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$failedJob = $this->model->find($id);
		if (!$failedJob) {
			throw new HttpResponseException(response()->json(['Model FailedJob not found.'], 404));
		}
		$failedJob->delete();
	}
}