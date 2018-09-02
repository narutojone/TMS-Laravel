<?php
 
namespace App\Repositories\SubtaskModuleTemplate;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplate;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplateInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositorySubtaskModuleTemplate extends BaseEloquentRepository implements SubtaskModuleTemplateInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositorySubtaskModuleTemplate constructor.
	 *
	 * @param App\Respositories\SubtaskModuleTemplate\SubtaskModuleTemplate $model
	 */
	public function __construct(SubtaskModuleTemplate $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new SubtaskModuleTemplate.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\SubtaskModuleTemplate\SubtaskModuleTemplate
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a SubtaskModuleTemplate.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\SubtaskModuleTemplate\SubtaskModuleTemplate
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$subtaskModuleTemplate = $this->find($input['id']);
        if ($subtaskModuleTemplate) {
            $subtaskModuleTemplate->fill($input);
            $subtaskModuleTemplate->save();
            return $subtaskModuleTemplate;
		}
		
		throw new HttpResponseException(response()->json(['Model SubtaskModuleTemplate not found.'], 404));
	}
 
	/**
	 * Delete a SubtaskModuleTemplate.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$subtaskModuleTemplate = $this->model->find($id);
		if (!$subtaskModuleTemplate) {
			throw new HttpResponseException(response()->json(['Model SubtaskModuleTemplate not found.'], 404));
		}
		$subtaskModuleTemplate->delete();
	}
}