<?php

namespace App\Repositories\ZendeskGroup;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ZendeskGroup
 */
class ZendeskGroupCreateRequest extends Request {

	protected $message = 'Request parameters for ZendeskGroup are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'group_id' => 'required|max:20',
            'name'     => 'required|max:50',
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
