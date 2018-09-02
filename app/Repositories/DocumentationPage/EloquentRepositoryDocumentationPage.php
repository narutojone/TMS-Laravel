<?php

namespace App\Repositories\DocumentationPage;

use App\Repositories\BaseEloquentRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryDocumentationPage extends BaseEloquentRepository implements DocumentationPageInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryDocumentationPage constructor.
     *
     * @param DocumentationPage $model
     */
    public function __construct(DocumentationPage $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new DocumentationPage.
     *
     * @param array $input
     *
     * @return DocumentationPage
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $documentationPage = $this->model->create($input);

        return $documentationPage;
    }

    /**
     * Update a DocumentationPage.
     *
     * @param integer $id
     * @param array $input
     *
     * @return DocumentationPage
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $documentationPage = $this->find($id);
        if ($documentationPage) {
            $documentationPage->fill($input);
            $documentationPage->save();
            return $documentationPage;
        }

        throw new ModelNotFoundException('Model DocumentationPage not found', 404);
    }

    /**
     * Delete a DocumentationPage.
     *
     * @param integer $id
     *
     * @throws Exception
     */
    public function delete($id)
    {
        $documentationPage = $this->model->find($id);
        if (!$documentationPage) {
            throw new ModelNotFoundException('Model DocumentationPage not found', 404);
        }
        if ($documentationPage->childPages->count()) {
            throw new Exception('Can not be deleted as it has sub pages', 403);
        }

        $documentationPage->delete();
    }

    protected function prepareCreateData(array $input)
    {
        return $input;
    }

    private function prepareUpdateData($id, $input)
    {
        return $input;
    }

    public function getPagesForParentPageDropdown($except_id = null)
    {
        return $this->model->where('id', '!=', $except_id)->pluck('title', 'id')->toArray();
    }
}