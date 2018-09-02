<?php
 
namespace App\Repositories\MatchingQueue;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\MatchingQueue\MatchingQueue;
use App\Repositories\MatchingQueue\MatchingQueueInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryMatchingQueue extends BaseEloquentRepository implements MatchingQueueInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryMatchingQueue constructor.
	 *
	 * @param App\Respositories\MatchingQueue\MatchingQueue $model
	 */
	public function __construct(MatchingQueue $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new MatchingQueue.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\MatchingQueue\MatchingQueue
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a MatchingQueue.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\MatchingQueue\MatchingQueue
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$matchingQueue = $this->find($input['id']);
        if ($matchingQueue) {
            $matchingQueue->fill($input);
            $matchingQueue->save();
            return $matchingQueue;
		}
		
		throw new HttpResponseException(response()->json(['Model MatchingQueue not found.'], 404));
	}
 
	/**
	 * Delete a MatchingQueue.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$matchingQueue = $this->model->find($id);
		if (!$matchingQueue) {
			throw new HttpResponseException(response()->json(['Model MatchingQueue not found.'], 404));
		}
		$matchingQueue->delete();
	}
}