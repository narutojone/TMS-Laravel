<?php namespace App\Repositories\FailedJob;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an FailedJob via api request
 */
class FailedJobCreateRequest extends Request {

	protected $message = 'Request parameters for FailedJob are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // some laravel validations
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
