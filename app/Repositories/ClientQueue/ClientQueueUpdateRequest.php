<?php namespace App\Repositories\ClientQueue;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ClientQueue entity via api request
 */
class ClientQueueUpdateRequest extends Request {

	protected $message = 'Request parameters for ClientQueue are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // some validations here
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
