<?php namespace App\Repositories\UserTaskType;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an UserTaskType entity via api request
 */
class UserTaskTypeUpdateRequest extends Request {

	protected $message = 'Request parameters for UserTaskType are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // Laravel validation rule
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
}
