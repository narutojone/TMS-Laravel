<?php namespace App\Repositories\ClientEmployeeLog;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ClientEmployeeLog via api request
 */
class ClientEmployeeLogCreateRequest extends Request {

	protected $message = 'Request parameters for ClientEmployeeLog are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            //laravel validation rules here
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
