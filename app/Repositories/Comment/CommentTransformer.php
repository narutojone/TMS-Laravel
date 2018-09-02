<?php
namespace App\Repositories\Comment;

use League\Fractal\TransformerAbstract;
use App\Repositories\Comment\Comment;

/**
 * CommentTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class CommentTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Comment object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Comment $comment)
    {
        return $comment->toArray();
    }
} 