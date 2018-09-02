<?php
namespace App\Repositories\GithubMilestone;

use League\Fractal\TransformerAbstract;

/**
 * TaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class GithubMilestoneTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Task object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(GithubMilestone $issue)
    {
        return $issue->toArray();
    }
} 