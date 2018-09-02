<?php

namespace App\Repositories\TemplateVersion;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryTemplateVersion extends BaseEloquentRepository implements TemplateVersionInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplateVersion constructor.
     *
     * @param TemplateVersion $model
     */
    public function __construct(TemplateVersion $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new EloquentRepositoryTemplateVersion.
     *
     * @param array $input
     *
     * @return EloquentRepositoryTemplateVersion
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if (!$this->isValid('create', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }
        return $this->model->create($input);
    }

    /**
     * Update a TemplateVersion.
     *
     * @param integer $id
     * @param array $input
     *
     * @return TemplateVersion
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if (!$this->isValid('update', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }

        $templateVersion = $this->find($id);
        if ($templateVersion) {
            $templateVersion->fill($input);
            $templateVersion->save();
            return $templateVersion;
        }

        throw new HttpResponseException(response()->json(['Model TemplateVersion not found.'], 404));
    }

    /**
     * Delete a TemplateVersion.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $templateVersion = $this->model->find($id);
        if (!$templateVersion) {
            throw new HttpResponseException(response()->json(['Model TemplateVersion not found.'], 404));
        }
        $templateVersion->delete();
    }

    private function prepareCreateData($input)
    {
        if(!isset($input['description']) || is_null($input['description'])) {
            $input['description'] = '';
        }

        return $input;
    }

    private function prepareUpdateData($input)
    {
        if(array_key_exists('description', $input) && is_null($input['description'])) {
            $input['description'] = '';
        }
        return $input;
    }
}