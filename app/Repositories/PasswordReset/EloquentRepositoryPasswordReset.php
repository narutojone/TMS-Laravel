<?php
 
namespace App\Repositories\PasswordReset;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\PasswordReset\PasswordReset;
use App\Repositories\PasswordReset\PasswordResetInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryPasswordReset extends BaseEloquentRepository implements PasswordResetInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryPasswordReset constructor.
	 *
	 * @param App\Respositories\PasswordReset\PasswordReset $model
	 */
	public function __construct(PasswordReset $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new PasswordReset.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\PasswordReset\PasswordReset
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a PasswordReset.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\PasswordReset\PasswordReset
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$passwordReset = $this->find($input['id']);
        if ($passwordReset) {
            $passwordReset->fill($input);
            $passwordReset->save();
            return $passwordReset;
		}
		
		throw new HttpResponseException(response()->json(['Model PasswordReset not found.'], 404));
	}
 
	/**
	 * Delete a PasswordReset.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$passwordReset = $this->model->find($id);
		if (!$passwordReset) {
			throw new HttpResponseException(response()->json(['Model PasswordReset not found.'], 404));
		}
		$passwordReset->delete();
	}
}