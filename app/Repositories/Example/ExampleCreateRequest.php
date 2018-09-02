<?php namespace App\Repositories\Example;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Example via api request
 */
class ExampleCreateRequest extends Request {

	protected $message = 'Request parameters for Example are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            'user_id'   			=> 'required|exists:users,id',
            'some_name'				=> 'required|max:100',
            'some_boolean'          => 'required|boolean',
            'some_json'             => 'required',
            'some_enum'             => 'required|in:first_enum,second_enum'
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
