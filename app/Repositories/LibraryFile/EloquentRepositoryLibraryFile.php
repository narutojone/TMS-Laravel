<?php
 
namespace App\Repositories\LibraryFile;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\LibraryFile\LibraryFile;
use App\Repositories\LibraryFile\LibraryFileInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryLibraryFile extends BaseEloquentRepository implements LibraryFileInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryLibraryFile constructor.
	 *
	 * @param App\Respositories\LibraryFile\LibraryFile $model
	 */
	public function __construct(LibraryFile $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new LibraryFile.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\LibraryFile\LibraryFile
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a LibraryFile.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\LibraryFile\LibraryFile
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$libraryFile = $this->find($input['id']);
        if ($libraryFile) {
            $libraryFile->fill($input);
            $libraryFile->save();
            return $libraryFile;
		}
		
		throw new HttpResponseException(response()->json(['Model LibraryFile not found.'], 404));
	}
 
	/**
	 * Delete a LibraryFile.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$libraryFile = $this->model->find($id);
		if (!$libraryFile) {
			throw new HttpResponseException(response()->json(['Model LibraryFile not found.'], 404));
		}
		$libraryFile->delete();
	}
}