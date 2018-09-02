<?php namespace App\Repositories\NotifierLog;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an NotifierLog via api request
 */
class NotifierLogCreateRequest extends Request {

	protected $message = 'Request parameters for NotifierLog are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // laravel validatoin rules
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
