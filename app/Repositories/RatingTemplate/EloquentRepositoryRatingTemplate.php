<?php
 
namespace App\Repositories\RatingTemplate;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryRatingTemplate extends BaseEloquentRepository implements RatingTemplateInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryRatingTemplate constructor.
     *
     * @param RatingTemplate $model
     */
    public function __construct(RatingTemplate $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new RatingTemplate.
     *
     * @param array $input
     * @return \App\Repositories\RatingTemplate\RatingTemplate
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
     * Update a RatingTemplate.
     *
     * @param integer $id
     * @param array $input
     * @return \App\Repositories\RatingTemplate\RatingTemplate
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }
        
        $ratingTemplate = $this->find($id);
        if ($ratingTemplate) {
            $ratingTemplate->fill($input);
            $ratingTemplate->save();
            return $ratingTemplate;
        }

        throw new ModelNotFoundException('Model RatingTemplate not found', 404);
    }
 
    /**
     * Delete a RatingTemplate.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $ratingTemplate = $this->model->find($id);
        if (!$ratingTemplate) {
            throw new ModelNotFoundException('Model RatingTemplate not found', 404);
        }
        $ratingTemplate->delete();
    }
}