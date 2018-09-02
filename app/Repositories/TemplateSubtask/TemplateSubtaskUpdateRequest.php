<?php

namespace App\Repositories\TemplateSubtask;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an TemplateSubtask entity via api request
 */
class TemplateSubtaskUpdateRequest extends Request {

	protected $message = 'Request parameters for TemplateSubtask are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'title'       => 'sometimes|max:255',
            'description' => 'sometimes|nullable',
            'version'     => 'required|boolean',
            'active'      => 'sometimes|boolean',
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
