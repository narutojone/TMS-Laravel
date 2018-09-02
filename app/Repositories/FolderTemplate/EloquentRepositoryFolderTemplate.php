<?php
 
namespace App\Repositories\FolderTemplate;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\FolderTemplate\FolderTemplate;
use App\Repositories\FolderTemplate\FolderTemplateInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryFolderTemplate extends BaseEloquentRepository implements FolderTemplateInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryFolderTemplate constructor.
	 *
	 * @param App\Respositories\FolderTemplate\FolderTemplate $model
	 */
	public function __construct(FolderTemplate $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new FolderTemplate.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\FolderTemplate\FolderTemplate
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a FolderTemplate.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\FolderTemplate\FolderTemplate
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$folderTemplate = $this->find($input['id']);
        if ($folderTemplate) {
            $folderTemplate->fill($input);
            $folderTemplate->save();
            return $folderTemplate;
		}
		
		throw new HttpResponseException(response()->json(['Model FolderTemplate not found.'], 404));
	}
 
	/**
	 * Delete a FolderTemplate.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$folderTemplate = $this->model->find($id);
		if (!$folderTemplate) {
			throw new HttpResponseException(response()->json(['Model FolderTemplate not found.'], 404));
		}
		$folderTemplate->delete();
	}
}