<?php
namespace App\Repositories\File;

use League\Fractal\TransformerAbstract;
use App\Repositories\File\File;

/**
 * FileTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FileTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the File object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(File $file)
    {
        return $file->toArray();
    }
} 