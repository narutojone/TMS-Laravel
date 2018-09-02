<?php
 
namespace App\Repositories\ZendeskUser;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\ZendeskUser\ZendeskUser;
use App\Repositories\ZendeskUser\ZendeskUserInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryZendeskUser extends BaseEloquentRepository implements ZendeskUserInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryZendeskUser constructor.
	 *
	 * @param App\Respositories\ZendeskUser\ZendeskUser $model
	 */
	public function __construct(ZendeskUser $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new ZendeskUser.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\ZendeskUser\ZendeskUser
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a ZendeskUser.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\ZendeskUser\ZendeskUser
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$zendeskUser = $this->find($input['id']);
        if ($zendeskUser) {
            $zendeskUser->fill($input);
            $zendeskUser->save();
            return $zendeskUser;
		}
		
		throw new HttpResponseException(response()->json(['Model ZendeskUser not found.'], 404));
	}
 
	/**
	 * Delete a ZendeskUser.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$zendeskUser = $this->model->find($id);
		if (!$zendeskUser) {
			throw new HttpResponseException(response()->json(['Model ZendeskUser not found.'], 404));
		}
		$zendeskUser->delete();
	}
}