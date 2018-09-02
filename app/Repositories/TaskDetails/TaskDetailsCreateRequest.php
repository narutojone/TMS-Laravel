<?php

namespace App\Repositories\TaskDetails;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an TaskDetails via api request
 */
class TaskDetailsCreateRequest extends Request {

	protected $message = 'Request parameters for TaskDetails are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'task_id'     => 'required|numeric|exists:tasks,id',
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
