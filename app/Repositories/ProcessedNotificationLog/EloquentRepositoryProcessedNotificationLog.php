<?php
 
namespace App\Repositories\ProcessedNotificationLog;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryProcessedNotificationLog extends BaseEloquentRepository implements ProcessedNotificationLogInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryProcessedNotificationLog constructor.
     *
     * @param \App\Repositories\ProcessedNotificationLog\ProcessedNotificationLog $model
     */
    public function __construct(ProcessedNotificationLog $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new ProcessedNotificationLog.
     *
     * @param array $input
     *
     * @return \App\Repositories\ProcessedNotificationLog\ProcessedNotificationLog
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    /**
     * Update a ProcessedNotificationLog.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $processedNotificationLog = $this->find($id);
        if ($processedNotificationLog) {
            $processedNotificationLog->fill($input);
            $processedNotificationLog->save();
            return $processedNotificationLog;
        }

        throw new ModelNotFoundException('Model Subtask not found.', 404);
    }

    /**
     * Delete a ProcessedNotificationLog.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $processedNotificationLog = $this->model->find($id);
        if (!$processedNotificationLog) {
            throw new ModelNotFoundException('Model Subtask not found.', 404);
        }
        $processedNotificationLog->delete();
    }

    /**
     * Prepare data for create action
     *
     * @param $input
     * @return array
     */
    protected function prepareCreateData(array $input) : array
    {
        $input['data'] = json_encode($input['data']);

        return $input;
    }

    /**
     * Generate the url path for the current page, with filters applied
     *
     * @param Request $request
     * @return string
     */
    public function generatePagePathWithFilterParams(Request $request)
    {
        // Generate the page path with filter parameters
        return url()->current().'?status='.$request->input('status');
    }


    /**
     * Add to each element of the collection a new property with decoded data
     *
     * @param LengthAwarePaginator $processedNotificationLogs
     * @return LengthAwarePaginator
     */
    public function getMappedNotifications(LengthAwarePaginator $processedNotificationLogs) : LengthAwarePaginator
    {
        $processedNotificationLogs->map(function ($processedNotificationLog) {
            $processedNotificationLog->decodedData = json_decode($processedNotificationLog->data, true);

            return $processedNotificationLog;
        });

        return $processedNotificationLogs;
    }
}