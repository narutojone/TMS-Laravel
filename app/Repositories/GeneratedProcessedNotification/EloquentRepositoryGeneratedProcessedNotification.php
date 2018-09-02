<?php
 
namespace App\Repositories\GeneratedProcessedNotification;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGeneratedProcessedNotification extends BaseEloquentRepository implements GeneratedProcessedNotificationInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGeneratedProcessedNotification constructor.
     *
     * @param \App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotification $model
     */
    public function __construct(GeneratedProcessedNotification $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new GeneratedProcessedNotification.
     *
     * @param array $input
     *
     * @return \App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotification
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    /**
     * Update a GeneratedProcessedNotification.
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

        $generatedProcessedNotification = $this->find($id);
        if ($generatedProcessedNotification) {
            $generatedProcessedNotification->fill($input);
            $generatedProcessedNotification->save();
            return $generatedProcessedNotification;
        }

        throw new ModelNotFoundException('Model Subtask not found.', 404);
    }

    /**
     * Delete a GeneratedProcessedNotification.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $generatedProcessedNotification = $this->model->find($id);
        if (!$generatedProcessedNotification) {
            throw new ModelNotFoundException('Model Subtask not found.', 404);
        }
        $generatedProcessedNotification->delete();
    }
}