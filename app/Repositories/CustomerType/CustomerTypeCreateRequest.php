<?php namespace App\Repositories\CustomerType;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an CustomerType via api request
 */
class CustomerTypeCreateRequest extends Request {

	protected $message = 'Request parameters for CustomerType are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // some laravel validations here
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
