<?php namespace App\Repositories\UserCustomerType;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an UserCustomerType entity via api request
 */
class UserCustomerTypeUpdateRequest extends Request {

	protected $message = 'Request parameters for UserCustomerType are not valid.';

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
