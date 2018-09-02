<?php
 
namespace App\Repositories\NotifierLog;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\NotifierLog\NotifierLog;
use App\Repositories\NotifierLog\NotifierLogInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryNotifierLog extends BaseEloquentRepository implements NotifierLogInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryNotifierLog constructor.
	 *
	 * @param App\Respositories\NotifierLog\NotifierLog $model
	 */
	public function __construct(NotifierLog $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new NotifierLog.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\NotifierLog\NotifierLog
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a NotifierLog.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\NotifierLog\NotifierLog
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$notifierLog = $this->find($input['id']);
        if ($notifierLog) {
            $notifierLog->fill($input);
            $notifierLog->save();
            return $notifierLog;
		}
		
		throw new HttpResponseException(response()->json(['Model NotifierLog not found.'], 404));
	}
 
	/**
	 * Delete a NotifierLog.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$notifierLog = $this->model->find($id);
		if (!$notifierLog) {
			throw new HttpResponseException(response()->json(['Model NotifierLog not found.'], 404));
		}
		$notifierLog->delete();
	}
}