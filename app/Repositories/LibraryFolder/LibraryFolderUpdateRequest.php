<?php namespace App\Repositories\LibraryFolder;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an LibraryFolder entity via api request
 */
class LibraryFolderUpdateRequest extends Request {

	protected $message = 'Request parameters for LibraryFolder are not valid.';

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */

	public function rules()
	{
		return [
            // laravel validation rule
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
