<?php
 
namespace App\Repositories\LibraryFolder;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\LibraryFolder\LibraryFolder;
use App\Repositories\LibraryFolder\LibraryFolderInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryLibraryFolder extends BaseEloquentRepository implements LibraryFolderInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryLibraryFolder constructor.
	 *
	 * @param App\Respositories\LibraryFolder\LibraryFolder $model
	 */
	public function __construct(LibraryFolder $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new LibraryFolder.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\LibraryFolder\LibraryFolder
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a LibraryFolder.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\LibraryFolder\LibraryFolder
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$libraryFolder = $this->find($input['id']);
        if ($libraryFolder) {
            $libraryFolder->fill($input);
            $libraryFolder->save();
            return $libraryFolder;
		}
		
		throw new HttpResponseException(response()->json(['Model LibraryFolder not found.'], 404));
	}
 
	/**
	 * Delete a LibraryFolder.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$libraryFolder = $this->model->find($id);
		if (!$libraryFolder) {
			throw new HttpResponseException(response()->json(['Model LibraryFolder not found.'], 404));
		}
		$libraryFolder->delete();
	}
}