<?php namespace App\Repositories\PasswordReset;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an PasswordReset via api request
 */
class PasswordResetCreateRequest extends Request {

	protected $message = 'Request parameters for PasswordReset are not valid.';

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
