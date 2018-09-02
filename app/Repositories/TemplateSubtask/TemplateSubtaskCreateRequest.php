<?php

namespace App\Repositories\TemplateSubtask;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an TemplateSubtask via api request
 */
class TemplateSubtaskCreateRequest extends Request {

	protected $message = 'Request parameters for TemplateSubtask are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'title'         => 'required|max:255',
            'description'   => 'sometimes|nullable',
            'active'        => 'sometimes|numeric|boolean',
            'add-to-tasks'  => 'sometimes|boolean',
            'min-date'      => 'sometimes|nullable|date_format:Y-m-d',
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
