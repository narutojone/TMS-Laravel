<?php
 
namespace App\Repositories\ClientEmployeeLog;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLogInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryClientEmployeeLog extends BaseEloquentRepository implements ClientEmployeeLogInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryClientEmployeeLog constructor.
	 *
	 * @param App\Respositories\ClientEmployeeLog\ClientEmployeeLog $model
	 */
	public function __construct(ClientEmployeeLog $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new ClientEmployeeLog.
	 *
	 * @param array $input
	 *
	 * @return App\Respositories\ClientEmployeeLog\ClientEmployeeLog
	 */
	public function create(array $input)
	{
		if(!$this->isValid('create', $input)) {
			throw new ValidationException($this->validators['create']);
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a ClientEmployeeLog.
	 *
	 * @param integer $id
	 * @param array $input
	 *
	 * @return App\Repositories\ClientEmployeeLog\ClientEmployeeLog
	 */
	public function update($id, array $input)
	{
		if(!$this->isValid('update', $input)) {
			throw new ValidationException($this->validators['update']);
		}
		
		$clientEmployeeLog = $this->find($id);
        if ($clientEmployeeLog) {
            $clientEmployeeLog->fill($input);
            $clientEmployeeLog->save();
            return $clientEmployeeLog;
		}
		
		throw new HttpResponseException(response()->json(['Model ClientEmployeeLog not found.'], 404));
	}
 
	/**
	 * Delete a ClientEmployeeLog.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$clientEmployeeLog = $this->model->find($id);
		if (!$clientEmployeeLog) {
			throw new HttpResponseException(response()->json(['Model ClientEmployeeLog not found.'], 404));
		}
		$clientEmployeeLog->delete();
	}
}