<?php namespace App\Repositories\MatchingQueue;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an MatchingQueue entity via api request
 */
class MatchingQueueUpdateRequest extends Request {

	protected $message = 'Request parameters for MatchingQueue are not valid.';

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
