<?php
namespace App\Repositories\Example;

use League\Fractal\TransformerAbstract;
use App\Repositories\Example\Example;
use App\Repositories\Example\SomeOtherTransformer;

/**
 * ExampleTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ExampleTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = ['user'];

    /**
     * List of resources that are available to be added in the example object response
     *
     * @var array
     */
    protected $availableIncludes = ['user'];

    public function transform(Example $example)
    {
        return $example->toArray();
    }

    /**
     * Include User
     *
     * @param Example $example
     * @return \League\Fractal\ItemResource
     */
    public function includeUser(Example $example)
    {
        $user[] = $example->user;

        // return $this->collection($user, new UserTransformer());
        // Here we should have a UserTransformer, but for the purpose of this demo I will use SomeOtherTransformer, as we do not have for the moment the User repository
        return $this->collection($user, new SomeOtherTransformer());
    }
} 