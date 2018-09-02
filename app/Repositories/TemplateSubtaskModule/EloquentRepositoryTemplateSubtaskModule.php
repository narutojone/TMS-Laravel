<?php
 
namespace App\Repositories\TemplateSubtaskModule;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplateSubtaskModule extends BaseEloquentRepository implements TemplateSubtaskModuleInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplateSubtaskModule constructor.
     *
     * @param TemplateSubtaskModule $model
     */
    public function __construct(TemplateSubtaskModule $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TemplateSubtaskModule.
     *
     * @param array $input
     *
     * @return TemplateSubtaskModule
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
     * Update a TemplateSubtaskModule.
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

        $templateSubtaskModule = $this->find($id);
        if ($templateSubtaskModule) {
            $templateSubtaskModule->fill($input);
            $templateSubtaskModule->save();
            return $templateSubtaskModule;
        }

        throw new ModelNotFoundException('Model User not found', 404);
    }

    /**
     * Delete a TemplateSubtaskModule.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $templateSubtaskModule = $this->model->find($id);
        if (!$templateSubtaskModule) {
            throw new ModelNotFoundException('Model User not found', 404);
        }
        $templateSubtaskModule->delete();
    }
}