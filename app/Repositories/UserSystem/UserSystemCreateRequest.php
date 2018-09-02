<?php namespace App\Repositories\UserSystem;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an UserSystem via api request
 */
class UserSystemCreateRequest extends Request {

	protected $message = 'Request parameters for UserSystem are not valid.';

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
