<?php
namespace App\Repositories\Faq;

use League\Fractal\TransformerAbstract;
use App\Repositories\Faq\Faq;

/**
 * FaqTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FaqTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Faq object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Faq $faq)
    {
        return $faq->toArray();
    }
} 