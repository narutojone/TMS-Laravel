<?php
 
namespace App\Repositories\PhoneSystemLog;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryPhoneSystemLog extends BaseEloquentRepository implements PhoneSystemLogInterface
{
	/**
	 * @var $model
	 */
	protected $model;

    /**
     * EloquentRepositoryPhoneSystemLog constructor.
     *
     * @param PhoneSystemLog $model
     */
	public function __construct(PhoneSystemLog $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}

    /**
     * Create a new PhoneSystemLog.
     *
     * @param array $input
     *
     * @return PhoneSystemLog
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $task = $this->model->create($input);

        return $task;
    }

    /**
     * Update a PhoneSystemLog.
     *
     * @param integer $id
     * @param array $input
     *
     * @return PhoneSystemLog
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
	{
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }
		
		$phoneSystemLog = $this->find($id);
        if ($phoneSystemLog) {
            $phoneSystemLog->fill($input);
            $phoneSystemLog->save();
            return $phoneSystemLog;
		}
        throw new ModelNotFoundException('Model PhoneSystemLog not found', 404);
	}

	/**
	 * Delete a PhoneSystemLog.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$phoneSystemLog = $this->model->find($id);
		if (!$phoneSystemLog) {
			throw new HttpResponseException(response()->json(['Model PhoneSystemLog not found.'], 404));
		}
		$phoneSystemLog->delete();
	}
}