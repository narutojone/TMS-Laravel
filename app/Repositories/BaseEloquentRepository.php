<?php
namespace App\Repositories;

use App\Repositories\BaseRepositoriesInterface;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AbstractRepository;
use Illuminate\Support\MessageBag;

/**
 * The base eloquent that implements methods available for all eloquents repos
 */
class BaseEloquentRepository extends AbstractRepository  implements BaseRepositoriesInterface
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new MessageBag());
    }
    
    function all(array $with = [])
    {
        $entity = $this->make($with);
        return $entity->get();
    }
    
    function get($id)
    {
        
    }

    function create(array $attributes)
    {

    }
    
    function update($id, array $attributes)
    {

    }
    
    function delete($id)
    {

    }
}