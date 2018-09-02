<?php namespace App\Repositories\ClientQueue;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ClientQueue via api request
 */
class ClientQueueCreateRequest extends Request {

	protected $message = 'Request parameters for ClientQueue are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // laravel validation here
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
