<?php
 
namespace App\Repositories\Comment;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryComment extends BaseEloquentRepository implements CommentInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryComment constructor.
     *
     * @param Comment $model
     */
    public function __construct(Comment $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Comment.
     *
     * @param array $input
     *
     * @return Comment
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a Comment.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $comment = $this->find($id);
        if ($comment) {
            $comment->fill($input);
            $comment->save();
            return $comment;
        }

        throw new ModelNotFoundException('Model Comment not found', 404);
    }

    /**
     * Delete a Comment.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $comment = $this->model->find($id);
        if (!$comment) {
            throw new ModelNotFoundException('Model Comment not found', 404);
        }
        $comment->delete();
    }
}