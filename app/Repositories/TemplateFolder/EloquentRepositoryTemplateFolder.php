<?php
 
namespace App\Repositories\TemplateFolder;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\TemplateFolder\TemplateFolder;
use App\Repositories\TemplateFolder\TemplateFolderInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplateFolder extends BaseEloquentRepository implements TemplateFolderInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryTemplateFolder constructor.
	 *
	 * @param App\Respositories\TemplateFolder\TemplateFolder $model
	 */
	public function __construct($model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new TemplateFolder.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\TemplateFolder\TemplateFolder
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $attributes)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($attributes);
	}
 
	/**
	 * Update a TemplateFolder.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\TemplateFolder\TemplateFolder
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $attributes)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$templateFolder = $this->find($attributes['id']);
        if ($templateFolder) {
            $templateFolder->fill($attributes);
            $templateFolder->save();
            return $templateFolder;
		}
		
		throw new HttpResponseException(response()->json(['Model TemplateFolder not found.'], 404));
	}
 
	/**
	 * Delete a TemplateFolder.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$templateFolder = $this->model->find($id);
		if (!$templateFolder) {
			throw new HttpResponseException(response()->json(['Model TemplateFolder not found.'], 404));
		}
		$templateFolder->delete();
	}

	/**
	 * Get All EmailTemplates.
	 *
	 * @return App\Respositories\TemplateFolder\TemplateFolder
	 */
	public function getAll()
	{
		$templateFolders = $this->model
			->orderBy('name', 'ASC')
			->paginate(10);

		if ($templateFolders) {
			return $templateFolders;
		}

		throw new HttpResponseException(response()->json(['Model TemplateFolder not found.'], 404));
	}

	/**
	 * Get FolderName.
	 *
	 * @return App\Respositories\TemplateFolder\TemplateFolder
	 */
	public function getTemplateFolderById($folder_id)
	{
		$templateFolder = $this->model
			->where('id', $folder_id)
			->first();

		if ($templateFolder) {
			return $templateFolder;
		}

		throw new HttpResponseException(response()->json(['Model TemplateFolder not found.'], 404));
	}
}