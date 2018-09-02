<?php

namespace App\Repositories\ClientPhone;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ClientPhone
 */
class ClientPhoneCreateRequest extends Request {

	protected $message = 'Request parameters for ClientPhone are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'client_id'  => 'required|numeric|exists:clients,id',
            'number'     => 'required|digits:10',
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
