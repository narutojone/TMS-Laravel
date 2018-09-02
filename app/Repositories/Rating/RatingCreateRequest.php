<?php namespace App\Repositories\Rating;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Rating via api request
 */
class RatingCreateRequest extends Request {

	protected $message = 'Request parameters for Rating are not valid.';

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
