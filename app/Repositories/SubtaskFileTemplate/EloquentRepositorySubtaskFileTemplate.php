<?php
 
namespace App\Repositories\SubtaskFileTemplate;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\SubtaskFileTemplate\SubtaskFileTemplate;
use App\Repositories\SubtaskFileTemplate\SubtaskFileTemplateInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositorySubtaskFileTemplate extends BaseEloquentRepository implements SubtaskFileTemplateInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositorySubtaskFileTemplate constructor.
	 *
	 * @param App\Respositories\SubtaskFileTemplate\SubtaskFileTemplate $model
	 */
	public function __construct(SubtaskFileTemplate $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new SubtaskFileTemplate.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\SubtaskFileTemplate\SubtaskFileTemplate
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a SubtaskFileTemplate.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\SubtaskFileTemplate\SubtaskFileTemplate
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$subtaskFileTemplate = $this->find($input['id']);
        if ($subtaskFileTemplate) {
            $subtaskFileTemplate->fill($input);
            $subtaskFileTemplate->save();
            return $subtaskFileTemplate;
		}
		
		throw new HttpResponseException(response()->json(['Model SubtaskFileTemplate not found.'], 404));
	}
 
	/**
	 * Delete a SubtaskFileTemplate.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$subtaskFileTemplate = $this->model->find($id);
		if (!$subtaskFileTemplate) {
			throw new HttpResponseException(response()->json(['Model SubtaskFileTemplate not found.'], 404));
		}
		$subtaskFileTemplate->delete();
	}
}