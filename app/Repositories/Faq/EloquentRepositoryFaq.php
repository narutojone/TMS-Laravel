<?php
 
namespace App\Repositories\Faq;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\FaqCategory\FaqCategoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryFaq extends BaseEloquentRepository implements FaqInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryFaq constructor.
     *
     * @param Faq $model
     */
    public function __construct(Faq $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new Faq.
     *
     * @param array $input
     * @return \App\Repositories\Faq\Faq
     * @throws ValidationException
     */
    public function create(array $input)
    {
        DB::beginTransaction();

        // check if the data from the request contains a new category
        $faqCategory = null;
        $faqCategoryRepository = app()->make(FaqCategoryInterface::class);

        if (isset($input['new_category']) && !empty($input['new_category'])) {
            $faqCategory = $faqCategoryRepository->create([
                'name' => $input['new_category'],
                'visible' => true,
            ]);
        } else {
            $faqCategory = $faqCategoryRepository->make()->find($input['faq_category_id']);
        }
        
        $input = $this->prepareCreateData($input, $faqCategory);
        
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $faqModel = $this->model->create($input);

        DB::commit();
        return $faqModel;
    }

    /**
     * Update a Faq.
     *
     * @param integer $id
     * @param array $input
     * @return \App\Repositories\Faq\Faq
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        DB::beginTransaction();

        // check if the data from the request contains a new category
        $faqCategory = null;
        $faqCategoryRepository = app()->make(FaqCategoryInterface::class);

        if (isset($input['new_category']) && !empty($input['new_category'])) {
            $faqCategory = $faqCategoryRepository->create([
                'name' => $input['new_category'],
                'visible' => true,
            ]);
        } else {
            $faqCategory = $faqCategoryRepository->make()->find($input['category']);
        }

        $input = $this->prepareUpdateData($input, $faqCategory);
        
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $faq = $this->find($id);
        if ($faq) {
            $faq->fill($input);
            $faq->save();
        }

        DB::commit();
        return $faq;

    }
 
    /**
     * Delete a Faq.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $faq = $this->model->find($id);
        if (!$faq) {
            throw new ModelNotFoundException('Model Faq not found', 404);
        }
        $faq->delete();
    }

    protected function prepareUpdateData(array $input, $faqCategory)
    {
        if ($faqCategory) {
            $input['faq_category_id'] = $faqCategory->id;
        }

        if(empty($input['content'])) {
            $input['content'] = '';
        }

        return $input;
    }

    protected function prepareCreateData(array $input, $faqCategory)
    {
        if ($faqCategory) {
            $input['faq_category_id'] = $faqCategory->id;
        }

        if(!isset($input['visible'])) {
            $input['visible'] = true;
        }

        if(!isset($input['active'])) {
            $input['active'] = true;
        }

        if(!isset($input['content']) || empty($input['content'])) {
            $input['content'] = '';
        }

        $input['order'] = 1;
        $faqModel = $this->model->where('faq_category_id', $faqCategory->id)->latest()->first();
        if ($faqModel) {
            $input['order'] = $faqModel->order + 1;     
        }   

        if (!isset($input['use_template']) || $input['use_template'] == 0) {
            $input['template_id'] = null;
        } 

        return $input;
    }

    public function move(int $id, string $direction)
    {
        $faq = $this->model->find($id);

        // Get the order number change for the reason
        $change = ($direction == Faq::DIRECTION_DOWN) ? 1 : -1;
        
        // Change place, if the other reason exists
        $other = $faq->faqCategory->faq()->where('order', $faq->order + $change)->first();
        if ($other) {
            $other->update(['order' => $faq->order]);
            $faq->update(['order' => $faq->order + $change]);
        }

        return $faq;
    }
}