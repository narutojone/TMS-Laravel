<?php
 
namespace App\Repositories\EmailTemplate;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\EmailTemplate\EmailTemplateInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryEmailTemplate extends BaseEloquentRepository implements EmailTemplateInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryEmailTemplate constructor.
	 *
	 * @param App\Respositories\EmailTemplate\EmailTemplate $model
	 */
	public function __construct(EmailTemplate $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new EmailTemplate.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\EmailTemplate\EmailTemplate
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a EmailTemplate.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\EmailTemplate\EmailTemplate
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$emailTemplate = $this->find($input['id']);
		if ($emailTemplate) {
				$emailTemplate->fill($input);
				$emailTemplate->save();
				return $emailTemplate;
		}
		
		throw new HttpResponseException(response()->json(['Model EmailTemplate not found.'], 404));
	}
 
	/**
	 * Delete a EmailTemplate.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$emailTemplate = $this->model->find($id);
		if (!$emailTemplate) {
			throw new HttpResponseException(response()->json(['Model EmailTemplate not found.'], 404));
		}
		$emailTemplate->delete();
	}

	/**
	 * Search EmailTemplates.
	 *
	 * @param string $name
	 *
	 * @return App\Respositories\EmailTemplate\EmailTemplate
	 */
	public function search($filter_name, $show_deactivated, $folder_id)
	{
		if ($folder_id == 0) {
			$emailTemplate = $this->model
			->where('name', 'like', '%' . $filter_name . '%')
			->where('active', !$show_deactivated)
			->orderBy('name', 'ASC')
			->paginate(10);
		} else {
			$emailTemplate = $this->model
			->where('name', 'like', '%' . $filter_name . '%')
			->where('active', !$show_deactivated)
			->where('folder_id', $folder_id)
			->orderBy('name', 'ASC')
			->paginate(10);
		}		

		if ($emailTemplate) {
			return $emailTemplate;
		}

		throw new HttpResponseException(response()->json(['Model EmailTemplate not found.'], 404));
	}

	/**
	 * Get All EmailTemplates.
	 *
	 * @return App\Respositories\EmailTemplate\EmailTemplate
	 */
	public function getAll()
	{
		$emailTemplate = $this->model
			->orderBy('name', 'ASC')
			->paginate(10);

		if ($emailTemplate) {
			return $emailTemplate;
		}

		throw new HttpResponseException(response()->json(['Model EmailTemplate not found.'], 404));
	}
}