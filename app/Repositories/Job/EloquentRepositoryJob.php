<?php
 
namespace App\Repositories\Job;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Job\Job;
use App\Repositories\Job\JobInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryJob extends BaseEloquentRepository implements JobInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryJob constructor.
	 *
	 * @param App\Respositories\Job\Job $model
	 */
	public function __construct(Job $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new Job.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\Job\Job
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a Job.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\Job\Job
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$job = $this->find($input['id']);
        if ($job) {
            $job->fill($input);
            $job->save();
            return $job;
		}
		
		throw new HttpResponseException(response()->json(['Model Job not found.'], 404));
	}
 
	/**
	 * Delete a Job.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$job = $this->model->find($id);
		if (!$job) {
			throw new HttpResponseException(response()->json(['Model Job not found.'], 404));
		}
		$job->delete();
	}
}