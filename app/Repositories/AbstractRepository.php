<?php
namespace App\Repositories;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\MessageBag;
/**
 * Base Repository class that implements some methods common to all repositories
 */
class AbstractRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * @var array
     */
    protected $validators;

    /**
     * Construct
     *
     * @param \Illuminate\Support\MessageBag $errors
     */
    public function __construct(MessageBag $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function make(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
     * Register Validators
     *
     * @param string $name
     * @param ValidableInterface $validator
     */
    public function registerValidator($name, $validator)
    {
        $this->validators[$name] = $validator;
    }

    /**
     * Check to see if the input data is valid
     *
     * @param string $name
     * @param array $data
     * @return boolean
     */
    public function isValid($name, array $data)
    {
        if ($this->validators[$name]->with($data)->passes()) {
            return true;
        }

        $this->errors = $this->validators[$name]->errors();

        return false;
    }

    /**
     * Get validaton errors from create or from update
     *
     * @return MessageBag
     */
    public function getValidationErrors()
    {
        return $this->errors;
    }

    /**
     * Retrieve all entities
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $with = array())
    {
        $entity = $this->make($with);

        return $entity->get();
    }

    /**
     * Find a single entity
     *
     * @param int $id
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $with = array())
    {
        $entity = $this->make($with);
        try {
            return $entity->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Entity not found');
        }
    }

    /**
     * Disable an entity
     * @param $id
     * @return bool
     */
    public function disable($id)
    {
        $entity = $this->find($id);

        if ($entity)
        {
            $entity->is_active = false;
            $entity->save();

            return true;
        }

        return false;
    }
}