<?php
 
namespace App\Repositories\FaqCategory;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryFaqCategory extends BaseEloquentRepository implements FaqCategoryInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryFaqCategory constructor.
     *
     * @param FaqCategory $model
     */
    public function __construct(FaqCategory $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new FaqCategory.
     *
     * @param array $input
     * @return FaqCategory
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
     * Update a FaqCategory.
     *
     * @param integer $id
     * @param array $input
     * @return FaqCategory
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }
        
        $faqCategory = $this->find($id);
        if ($faqCategory) {
            $faqCategory->fill($input);
            $faqCategory->save();

            return $faqCategory;
        }
        
        throw new ModelNotFoundException('Model FaqCategory not found', 404);
    }
 
    /**
     * Delete a FaqCategory.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $faqCategory = $this->model->find($id);
        if (!$faqCategory) {
            throw new ModelNotFoundException('Model FaqCategory not found', 404);
        }
        $faqCategory->delete();
    }

    /**
     * Prepare data for create action
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        if (isset($input['visible']) && ($input['visible'] == 'on' || $input['visible'] == true)) {
            $input['visible'] = true;
        } else {
            $input['visible'] = false;
        }

        $lastOrder = 0;
        $faqCategoryModel = $this->model->orderBy('order', 'DESC')->first();
        if ($faqCategoryModel) {
            $lastOrder = $faqCategoryModel->order;
        }
        $input['order'] = $lastOrder + 1;

        return $input;
    }

    /**
     * Prepare data for update action
     *
     * @param array $input
     * @return array
     */
    protected function prepareUpdateData(array $input)
    {
        return $input;
    }

    /**
     * Move the Faq Category up or down
     *
     * @param int $id - id of the resource to be moved
     * @param string $direction - direction of the move
     * @return FaqCategory
     */
    public function move(int $id, string $direction)
    {
        $faqCategory = $this->find($id);
        $change = ($direction == FaqCategory::DIRECTION_DOWN) ? 1 : -1;
        
        $other = $this->model->where('order', $faqCategory->order + $change)->first();

        // Change place, if the other reason exists
        if ($other) {
            $this->update($other->id, ['order' => $faqCategory->order]);
            $faqCategory = $this->update($id, ['order' => $faqCategory->order + $change]);
        }

        return $faqCategory;
    }
}