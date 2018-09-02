<?php
namespace App\Repositories\LibraryFolder;

use League\Fractal\TransformerAbstract;
use App\Repositories\LibraryFolder\LibraryFolder;

/**
 * LibraryFolderTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class LibraryFolderTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the LibraryFolder object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(LibraryFolder $libraryFolder)
    {
        return $libraryFolder->toArray();
    }
} 