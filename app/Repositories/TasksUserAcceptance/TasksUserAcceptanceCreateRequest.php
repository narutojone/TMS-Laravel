<?php namespace App\Repositories\TasksUserAcceptance;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an TasksUserAcceptance via api request
 */
class TasksUserAcceptanceCreateRequest extends Request {

	protected $message = 'Request parameters for TasksUserAcceptance are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // Laravel validation rules
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
