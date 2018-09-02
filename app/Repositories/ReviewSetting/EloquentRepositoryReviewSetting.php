<?php
 
namespace App\Repositories\ReviewSetting;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\ReviewSettingTemplate\ReviewSettingTemplateInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryReviewSetting extends BaseEloquentRepository implements ReviewSettingInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryReviewSetting constructor.
     *
     * @param ReviewSetting $model
     */
    public function __construct(ReviewSetting $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new ReviewSetting.
     *
     * @param array $input
     *
     * @return ReviewSetting
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
     * Update a ReviewSetting.
     *
     * @param integer $id
     * @param array $input
     *
     * @return ReviewSetting
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $reviewSetting = $this->find($id);
        if ($reviewSetting) {
            $reviewSetting->fill($input);
            $reviewSetting->save();
            return $reviewSetting;
        }

        throw new ModelNotFoundException('Model ReviewSetting not found.', 404);
    }

    /**
     * Update a ReviewSetting.
     *
     * @param array $input
     *
     * @return ReviewSetting
     * @throws ValidationException
     */
    public function updateCustom(array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $reviewSetting = $this->model->first();
        if (!$reviewSetting) {
            $reviewSetting = new ReviewSetting();
        }

        if ($reviewSetting) {
            $reviewSetting->fill($input);
            $reviewSetting->save();

            $reviewSettingTemplateRepository = app()->make(ReviewSettingTemplateInterface::class);
            $reviewSettingTemplateRepository->model()->where('review_setting_id', '=', $reviewSetting->id)->delete();

            if (isset($input['templates_for_review']) && !empty($input['templates_for_review'])) {

                if (count($input['templates_for_review']) < $reviewSetting->no_of_tasks_for_level_two) {
                    $errors = ValidationException::withMessages(['The number of templates selected can not be smaller than the number of no of tasks needed for review.']);
                    throw $errors;
                }

                $templatesToSave = [];
                $i = 0;
                foreach ($input['templates_for_review'] as $templateId) {
                    $templatesToSave[$i]['template_id'] = $templateId;
                    $templatesToSave[$i]['review_setting_id'] = $reviewSetting->id;

                    // Laravel's bulk insert does not add the timestamps by itself, so we have to add them
                    $templatesToSave[$i]['created_at'] = date('Y-m-d H:i:s');
                    $templatesToSave[$i]['updated_at'] = date('Y-m-d H:i:s');
                    $i++;
                }
                $reviewSettingTemplateRepository->model()->insert($templatesToSave);
            }

            return $reviewSetting;
        }



        throw new ModelNotFoundException('Model ReviewSetting not found.', 404);
    }

    /**
     * Delete a ReviewSetting.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $reviewSetting = $this->model->find($id);
        if (!$reviewSetting) {
            throw new ModelNotFoundException('Model ReviewSetting not found.', 404);
        }
        $reviewSetting->delete();
    }
}