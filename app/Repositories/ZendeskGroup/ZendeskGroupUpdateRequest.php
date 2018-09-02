<?php

namespace App\Repositories\ZendeskGroup;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ZendeskGroup
 */
class ZendeskGroupUpdateRequest extends Request {

	protected $message = 'Request parameters for ZendeskGroup are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'group_id' => 'sometimes|max:20',
            'name'     => 'sometimes|max:50',
            'url'      => 'sometimes|nullable',
            'deleted'  => 'sometimes|boolean',
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
