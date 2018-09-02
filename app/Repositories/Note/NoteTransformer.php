<?php
namespace App\Repositories\Note;

use League\Fractal\TransformerAbstract;
use App\Repositories\Note\Note;

/**
 * NoteTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class NoteTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Note object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Note $note)
    {
        return $note->toArray();
    }
} 