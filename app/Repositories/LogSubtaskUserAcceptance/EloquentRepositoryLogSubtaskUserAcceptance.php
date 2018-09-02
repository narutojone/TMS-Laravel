<?php
 
namespace App\Repositories\LogSubtaskUserAcceptance;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptance;
use App\Repositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptanceInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryLogSubtaskUserAcceptance extends BaseEloquentRepository implements LogSubtaskUserAcceptanceInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryLogSubtaskUserAcceptance constructor.
	 *
	 * @param App\Respositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptance $model
	 */
	public function __construct(LogSubtaskUserAcceptance $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new LogSubtaskUserAcceptance.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptance
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a LogSubtaskUserAcceptance.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptance
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$logSubtaskUserAcceptance = $this->find($input['id']);
        if ($logSubtaskUserAcceptance) {
            $logSubtaskUserAcceptance->fill($input);
            $logSubtaskUserAcceptance->save();
            return $logSubtaskUserAcceptance;
		}
		
		throw new HttpResponseException(response()->json(['Model LogSubtaskUserAcceptance not found.'], 404));
	}
 
	/**
	 * Delete a LogSubtaskUserAcceptance.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$logSubtaskUserAcceptance = $this->model->find($id);
		if (!$logSubtaskUserAcceptance) {
			throw new HttpResponseException(response()->json(['Model LogSubtaskUserAcceptance not found.'], 404));
		}
		$logSubtaskUserAcceptance->delete();
	}
}