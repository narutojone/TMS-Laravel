<?php
 
namespace App\Repositories\LogTaskUserAcceptance;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\LogTaskUserAcceptance\LogTaskUserAcceptance;
use App\Repositories\LogTaskUserAcceptance\LogTaskUserAcceptanceInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryLogTaskUserAcceptance extends BaseEloquentRepository implements LogTaskUserAcceptanceInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryLogTaskUserAcceptance constructor.
	 *
	 * @param App\Respositories\LogTaskUserAcceptance\LogTaskUserAcceptance $model
	 */
	public function __construct(LogTaskUserAcceptance $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new LogTaskUserAcceptance.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\LogTaskUserAcceptance\LogTaskUserAcceptance
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a LogTaskUserAcceptance.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\LogTaskUserAcceptance\LogTaskUserAcceptance
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$logTaskUserAcceptance = $this->find($input['id']);
        if ($logTaskUserAcceptance) {
            $logTaskUserAcceptance->fill($input);
            $logTaskUserAcceptance->save();
            return $logTaskUserAcceptance;
		}
		
		throw new HttpResponseException(response()->json(['Model LogTaskUserAcceptance not found.'], 404));
	}
 
	/**
	 * Delete a LogTaskUserAcceptance.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$logTaskUserAcceptance = $this->model->find($id);
		if (!$logTaskUserAcceptance) {
			throw new HttpResponseException(response()->json(['Model LogTaskUserAcceptance not found.'], 404));
		}
		$logTaskUserAcceptance->delete();
	}
}