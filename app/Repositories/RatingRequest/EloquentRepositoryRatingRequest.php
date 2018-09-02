<?php
 
namespace App\Repositories\RatingRequest;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\RatingRequest\RatingRequest;
use App\Repositories\RatingRequest\RatingRequestInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryRatingRequest extends BaseEloquentRepository implements RatingRequestInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryRatingRequest constructor.
	 *
	 * @param App\Respositories\RatingRequest\RatingRequest $model
	 */
	public function __construct(RatingRequest $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new RatingRequest.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\RatingRequest\RatingRequest
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a RatingRequest.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\RatingRequest\RatingRequest
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$ratingRequest = $this->find($input['id']);
        if ($ratingRequest) {
            $ratingRequest->fill($input);
            $ratingRequest->save();
            return $ratingRequest;
		}
		
		throw new HttpResponseException(response()->json(['Model RatingRequest not found.'], 404));
	}
 
	/**
	 * Delete a RatingRequest.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$ratingRequest = $this->model->find($id);
		if (!$ratingRequest) {
			throw new HttpResponseException(response()->json(['Model RatingRequest not found.'], 404));
		}
		$ratingRequest->delete();
	}
}