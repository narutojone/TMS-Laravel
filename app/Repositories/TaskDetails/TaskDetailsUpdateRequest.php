<?php

namespace App\Repositories\TaskDetails;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an TaskDetails entity via api request
 */
class TaskDetailsUpdateRequest extends Request {

	protected $message = 'Request parameters for TaskDetails are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'description' => 'sometimes|nullable',
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
