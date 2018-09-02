<?php
 
namespace App\Repositories\OverdueReason;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryOverdueReason extends BaseEloquentRepository implements OverdueReasonInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryOverdueReason constructor.
     *
     * @param OverdueReason $model
     */
    public function __construct(OverdueReason $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new OverdueReason.
     *
     * @param array $input
     * @return \App\Repositories\OverdueReason\OverdueReason
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
     * Update a OverdueReason.
     *
     * @param integer $id
     * @param array $input
     * @return \App\Repositories\OverdueReason\OverdueReason
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $overdueReason = $this->find($id);
        if ($overdueReason) {
            $overdueReason->fill($input);
            $overdueReason->save();

            return $overdueReason;
        }
        
        throw new ModelNotFoundException('Model OverdueReason not found', 404);
    }

    /**
     * Delete a OverdueReason.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $overdueReason = $this->model->find($id);
        if (!$overdueReason) {
            throw new ModelNotFoundException('Model OverdueReason not found', 404);
        }
        $overdueReason->delete();
    }

    /**
     * Get all active overdue reasons
     * @return mixed
     */
    public function getAllActive()
    {
        return $this->model->where('active', 1)->where('default', 1)->orderBy('priority', 'ASC')->get();
    }

    /**
     * Get last priority
     *
     * @return int
     */
    public function getLastPriority()
    {
        $overdueReason = $this->model->orderBy('priority', 'DESC')->first();

        $lastPriority = 0;
        if ($overdueReason) {
            $lastPriority = $overdueReason->priority;        
        }

        return $lastPriority;
    }

    /**
     * Prepare data to create the entity
     *
     * @param array $input
     * @return array
     */
    public function prepareCreateData(array $input)
    {
        $lastPriority = $this->getLastPriority();

        $input['priority'] = $lastPriority + 1;

        if(!isset($input['default'])) {
            $input['default'] = 1;
        }
 
        return $input;
    }

    /**
     * Prepare data for update action
     *
     * @param array $input - upate data
     * @return array
     */
    public function prepareUpdateData(array $input)
    {
        if(array_key_exists('description', $input) && is_null($input['description'])) {
            $input['description'] = '';
        }
    
        return $input;
    }

    /**
     * Move a entity up or down
     *
     * @param int $id - id of the reason entity to be moved
     * @param string $direction - the direction of "moving"
     * @return OverdueReason
     */
    public function move(int $id, string $direction)
    {
        $reason = $this->find($id);

        // Get the order number change for the reason
        $change = ($direction == OverdueReason::DIRECTION_DOWN) ? 1 : -1;

        // Get the other reason it will change place with
        $other = $this->make()->where('priority', $reason->priority + $change)->first();

        // Change place, if the other reason exists
        if ($other) {
            $other->priority = $reason->priority;
            $other->save();

            $reason->priority = $reason->priority + $change;
            $reason->save();
        }

        return $reason;
    }
}
