<?php
 
namespace App\Repositories\Note;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Note\Note;
use App\Repositories\Note\NoteInterface;
use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryNote extends BaseEloquentRepository implements NoteInterface
{
	/**
	 * @var $model
	 */
	protected $model;

	/**
	 * EloquentRepositoryNote constructor.
	 *
	 * @param App\Respositories\Note\Note $model
	 */
	public function __construct(Note $model)
	{
		parent::__construct();
		
		$this->model = $model;
	}
	
	/**
	 * Create a new Note.
	 *
	 * @param array $attributes
	 *
	 * @return App\Respositories\Note\Note
	 */
	public function create(array $attributes)
	{
		if(!$this->isValid('create', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		return $this->model->create($input);
	}
 
	/**
	 * Update a Note.
	 *
	 * @param integer $id
	 * @param array $attributes
	 *
	 * @return App\Respositories\Note\Note
	 */
	public function update($id, array $attributes)
	{
		if(!$this->isValid('update', $input)) {
			throw new HttpResponseException(response()->json($this->errors, 422));
		}
		
		$note = $this->find($input['id']);
        if ($note) {
            $note->fill($input);
            $note->save();
            return $note;
		}
		
		throw new HttpResponseException(response()->json(['Model Note not found.'], 404));
	}
 
	/**
	 * Delete a Note.
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$note = $this->model->find($id);
		if (!$note) {
			throw new HttpResponseException(response()->json(['Model Note not found.'], 404));
		}
		$note->delete();
	}
}