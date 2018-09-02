<?php
 
namespace App\Repositories\ReviewSettingTemplate;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryReviewSettingTemplate extends BaseEloquentRepository implements ReviewSettingTemplateInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryReviewSettingTemplate constructor.
     *
     * @param ReviewSettingTemplate $model
     */
    public function __construct(ReviewSettingTemplate $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new ReviewSettingTemplate.
     *
     * @param array $input
     *
     * @return ReviewSettingTemplate
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['crate']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a ReviewSettingTemplate.
     *
     * @param integer $id
     * @param array $input
     *
     * @return ReviewSettingTemplate
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $reviewSettingTemplate = $this->find($id);
        if ($reviewSettingTemplate) {
            $reviewSettingTemplate->fill($input);
            $reviewSettingTemplate->save();
            return $reviewSettingTemplate;
        }

        throw new ModelNotFoundException('Model ReviewSettingTemplate not found.', 404);
    }

    /**
     * Delete a ReviewSettingTemplate.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $reviewSettingTemplate = $this->model->find($id);
        if (!$reviewSettingTemplate) {
            throw new ModelNotFoundException('Model ReviewSettingTemplate not found.', 404);
        }
        $reviewSettingTemplate->delete();
    }
}