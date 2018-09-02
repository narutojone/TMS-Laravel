<?php
namespace App\Repositories\FaqCategory;

use League\Fractal\TransformerAbstract;
use App\Repositories\FaqCategory\FaqCategory;

/**
 * FaqCategoryTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FaqCategoryTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the FaqCategory object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(FaqCategory $faqCategory)
    {
        return $faqCategory->toArray();
    }
} 