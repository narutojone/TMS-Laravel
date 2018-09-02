<?php
namespace App\Repositories\Example;

use League\Fractal\TransformerAbstract;
use App\Repositories\Example\Example;
use App\Repositories\User\User;

/**
 * Just a simple transfirmer that we are going to use in the ExampleTransformer for the purpose of this demo
 */
class SomeOtherTransformer extends TransformerAbstract {

    public function transform(User $user)
    {
        return $user->toArray();
    }

} 