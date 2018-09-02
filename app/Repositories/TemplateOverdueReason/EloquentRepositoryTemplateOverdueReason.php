<?php
 
namespace App\Repositories\TemplateOverdueReason;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryTemplateOverdueReason extends BaseEloquentRepository implements TemplateOverdueReasonInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplateOverdueReason constructor.
     *
     * @param TemplateOverdueReason $model
     */
    public function __construct(TemplateOverdueReason $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new TemplateOverdueReason.
     *
     * @param array $input
     * @return TemplateOverdueReason
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $templateOverdueReason = $this->model->create($input);

        return $templateOverdueReason;
    }

    /**
     * Update a TemplateOverdueReason.
     *
     * @param integer $id
     * @param array $input
     * @return TemplateOverdueReason
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $templateOverdueReason = $this->find($id);
        if ($templateOverdueReason) {
            $templateOverdueReason->fill($input);
            $templateOverdueReason->save();

            return $templateOverdueReason;
        }
        throw new ModelNotFoundException('Model TemplateOverdueReason not found', 404);
    }

    /**
     * Delete a TemplateOverdueReason.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $templateOverdueReason = $this->model->find($id);
        if (!$templateOverdueReason) {
            throw new ModelNotFoundException('Model TemplateOverdueReason not found', 404);
        }
        $templateOverdueReason->delete();
    }

    /**
     * Prepare data for insert action
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input) : array
    {
        if($input['trigger_type'] == TemplateOverdueReason::TRIGGER_NONE) {
            $input['trigger_counter'] = null;
            $input['action'] = null;
        }
        return $input;
    }

    /**
     * Prepare data for update action
     *
     * @param array $input
     * @return array
     */
    protected function prepareUpdateData(array $input) : array
    {
        // Disable the updating of overdue reason ID and trigger type
        // If you want to change it then you need to remove the TemplateOverdueReason and create a new one
        if(isset($input['overdue_reason_id'])) {
            unset($input['overdue_reason_id']);
        }
        if(isset($input['trigger_type'])) {
            unset($input['trigger_type']);
        }

        return $input;
    }

}