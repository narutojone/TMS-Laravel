<?php
 
namespace App\Repositories\ClientQueue;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\ClientQueue\ClientQueue;
use App\Repositories\ClientQueue\ClientQueueInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryClientQueue extends BaseEloquentRepository implements ClientQueueInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryClientQueue constructor.
	 *
	 * @param App\Respositories\ClientQueue\ClientQueue $model
	 */
	public function __construct(ClientQueue $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new ClientQueue.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\ClientQueue\ClientQueue
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a ClientQueue.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\ClientQueue\ClientQueue
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$clientQueue = $this->find($input['id']);
        if ($clientQueue) {
            $clientQueue->fill($input);
            $clientQueue->save();
            return $clientQueue;
		}
		
		throw new HttpResponseException(response()->json(['Model ClientQueue not found.'], 404));
	}
 
	/**
	 * Delete a ClientQueue.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$clientQueue = $this->model->find($id);
		if (!$clientQueue) {
			throw new HttpResponseException(response()->json(['Model ClientQueue not found.'], 404));
		}
		$clientQueue->delete();
	}
}