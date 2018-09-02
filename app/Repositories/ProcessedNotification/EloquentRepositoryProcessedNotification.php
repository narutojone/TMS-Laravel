<?php
 
namespace App\Repositories\ProcessedNotification;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryProcessedNotification extends BaseEloquentRepository implements ProcessedNotificationInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryProcessedNotification constructor.
     *
     * @param \App\Repositories\ProcessedNotification\ProcessedNotification $model
     */
    public function __construct(ProcessedNotification $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new ProcessedNotification.
     *
     * @param array $input
     *
     * @return \App\Repositories\ProcessedNotification\ProcessedNotification
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
     * Update a ProcessedNotification.
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

        $processedNotification = $this->find($id);
        if ($processedNotification) {
            $processedNotification->fill($input);
            $processedNotification->save();
            return $processedNotification;
        }

        throw new ModelNotFoundException('Model Subtask not found.', 404);
    }

    /**
     * Delete a ProcessedNotification.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $processedNotification = $this->model->find($id);
        if (!$processedNotification) {
            throw new ModelNotFoundException('Model Subtask not found.', 404);
        }
        $processedNotification->delete();
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
     * @param LengthAwarePaginator $processedNotifications
     * @return LengthAwarePaginator
     */
    public function getMappedNotifications(LengthAwarePaginator $processedNotifications) : LengthAwarePaginator
    {
        $processedNotifications->map(function ($processedNotification) {
            $processedNotification->decodedData = json_decode($processedNotification->data, true);

            return $processedNotification;
        });

        return $processedNotifications;
    }
}