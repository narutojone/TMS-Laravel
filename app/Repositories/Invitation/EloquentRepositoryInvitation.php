<?php
 
namespace App\Repositories\Invitation;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Invitation\Invitation;
use App\Repositories\Invitation\InvitationInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryInvitation extends BaseEloquentRepository implements InvitationInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryInvitation constructor.
	 *
	 * @param App\Respositories\Invitation\Invitation $model
	 */
	public function __construct(Invitation $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new Invitation.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\Invitation\Invitation
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a Invitation.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\Invitation\Invitation
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$invitation = $this->find($input['id']);
        if ($invitation) {
            $invitation->fill($input);
            $invitation->save();
            return $invitation;
		}
		
		throw new HttpResponseException(response()->json(['Model Invitation not found.'], 404));
	}
 
	/**
	 * Delete a Invitation.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$invitation = $this->model->find($id);
		if (!$invitation) {
			throw new HttpResponseException(response()->json(['Model Invitation not found.'], 404));
		}
		$invitation->delete();
	}
}