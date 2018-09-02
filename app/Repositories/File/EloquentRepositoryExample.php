<?php
 
namespace App\Repositories\File;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\File\File;
use App\Repositories\File\FileInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryFile extends BaseEloquentRepository implements FileInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryFile constructor.
	 *
	 * @param App\Respositories\File\File $model
	 */
	public function __construct(File $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new File.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\File\File
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a File.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\File\File
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$file = $this->find($input['id']);
        if ($file) {
            $file->fill($input);
            $file->save();
            return $file;
		}
		
		throw new HttpResponseException(response()->json(['Model File not found.'], 404));
	}
 
	/**
	 * Delete a File.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$file = $this->model->find($id);
		if (!$file) {
			throw new HttpResponseException(response()->json(['Model File not found.'], 404));
		}
		$file->delete();
	}
}