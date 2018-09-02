<?php namespace App\Repositories\PhoneSystemLog;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an PhoneSystemLog via api request
 */
class PhoneSystemLogCreateRequest extends Request {

	protected $message = 'Request parameters for PhoneSystemLog are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // laravel validation rules
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
