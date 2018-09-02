<?php

namespace App\Repositories\GithubIssue;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGithubIssue extends BaseEloquentRepository implements GithubIssueInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGithubIssue.php constructor.
     *
     * @param GithubIssue $model
     */
    public function __construct(GithubIssue $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new GithubIssue.
     *
     * @param array $input
     *
     * @return GithubIssue
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $task = $this->model->create($input);

        return $task;
    }

    /**
     * Update a GithubIssue.
     *
     * @param integer $id
     * @param array $input
     *
     * @return GithubIssue
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $gitHubIssue = $this->find($id);
        if ($gitHubIssue) {
            $gitHubIssue->fill($input);
            $gitHubIssue->save();
            return $gitHubIssue;
        }
        throw new ModelNotFoundException('Model GithubIssue not found', 404);
    }

    /**
     * Delete a GithubIssue.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $gitHubIssue = $this->model->find($id);
        if (!$gitHubIssue) {
            throw new HttpResponseException(response()->json(['Model GithubIssue not found.'], 404));
        }
        $gitHubIssue->delete();
    }


    public function createJsonFormatForAutoComplete()
    {
        return $this->model
            ->select(['id', 'issue_number', 'issue_title'])
            ->orderBy('issue_number', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->issue_number,
                    'name' => sprintf('#%s  : %s', $item->issue_number, $item->issue_title)
                ];
            })->all();
    }
}