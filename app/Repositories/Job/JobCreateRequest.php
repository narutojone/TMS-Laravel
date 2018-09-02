<?php namespace App\Repositories\Job;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Job via api request
 */
class JobCreateRequest extends Request {

	protected $message = 'Request parameters for Job are not valid.';

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
