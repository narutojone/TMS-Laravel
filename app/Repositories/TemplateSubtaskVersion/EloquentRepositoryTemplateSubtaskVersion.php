<?php
 
namespace App\Repositories\TemplateSubtaskVersion;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModule;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModuleInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplateSubtaskVersion extends BaseEloquentRepository implements TemplateSubtaskVersionInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryTemplateSubtaskVersion constructor.
	 *
	 * @param TemplateSubtaskVersion $model
	 */
	public function __construct(TemplateSubtaskVersion $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new EloquentRepositoryTemplateSubtaskVersion.
	 *
	 * @param array $attributes
	 *
	 * @return EloquentRepositoryTemplateSubtaskVersion
	 */
	public function create(array $attributes)
	{
		$input = $this->prepareCreateData($attributes);
		
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a TemplateSubtaskVersion.
	 *
	 * @param integer $id
	 * @param array $input
	 *
	 * @return TemplateSubtaskVersion
	 */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if (!$this->isValid('update', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }

        $templateSubtaskVersion = $this->find($id);
        if ($templateSubtaskVersion) {
            $templateSubtaskVersion->fill($input);
            $templateSubtaskVersion->save();
            return $templateSubtaskVersion;
        }

        throw new HttpResponseException(response()->json(['Model TemplateSubtaskVersion not found.'], 404));
    }
 
	/**
	 * Delete a TemplateSubtaskVersion.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
    public function delete($id)
    {
        $templateSubtaskVersion = $this->model->find($id);
        if (!$templateSubtaskVersion) {
            throw new HttpResponseException(response()->json(['Model TemplateSubtaskVersion not found.'], 404));
        }
        $templateSubtaskVersion->delete();
    }

	private function prepareCreateData($input)
	{
		return $input;
	}

	private function prepareUpdateData($id, $input)
	{
		return $input;
	}
}