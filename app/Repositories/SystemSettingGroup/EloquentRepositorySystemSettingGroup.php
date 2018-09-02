<?php
 
namespace App\Repositories\SystemSettingGroup;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\SystemSettingGroup\SystemSettingGroup;
use App\Repositories\SystemSettingGroup\SystemSettingGroupInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositorySystemSettingGroup extends BaseEloquentRepository implements SystemSettingGroupInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositorySystemSettingGroup constructor.
	 *
	 * @param App\Respositories\SystemSettingGroup\SystemSettingGroup $model
	 */
	public function __construct(SystemSettingGroup $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new SystemSettingGroup.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\SystemSettingGroup\SystemSettingGroup
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a SystemSettingGroup.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\SystemSettingGroup\SystemSettingGroup
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$systemSettingGroup = $this->find($input['id']);
        if ($systemSettingGroup) {
            $systemSettingGroup->fill($input);
            $systemSettingGroup->save();
            return $systemSettingGroup;
		}
		
		throw new HttpResponseException(response()->json(['Model SystemSettingGroup not found.'], 404));
	}
 
	/**
	 * Delete a SystemSettingGroup.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$systemSettingGroup = $this->model->find($id);
		if (!$systemSettingGroup) {
			throw new HttpResponseException(response()->json(['Model SystemSettingGroup not found.'], 404));
		}
		$systemSettingGroup->delete();
	}
}