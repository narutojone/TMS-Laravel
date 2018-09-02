<?php

namespace App\Repositories\GithubMilestone;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGithubMilestone extends BaseEloquentRepository implements GithubMilestoneInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGithubMilestone constructor.
     *
     * @param GithubMilestone $model
     */
    public function __construct(GithubMilestone $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new GithubMilestone.
     *
     * @param array $input
     *
     * @return GithubMilestone
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
     * @return GithubMilestone
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $gitHubMilestone = $this->find($id);
        if ($gitHubMilestone) {
            $gitHubMilestone->fill($input);
            $gitHubMilestone->save();
            return $gitHubMilestone;
        }
        throw new ModelNotFoundException('Model GithubMilestone not found', 404);
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
        $gitHubMilestone = $this->model->find($id);
        if (!$gitHubMilestone) {
            throw new HttpResponseException(response()->json(['Model GithubMilestone not found.'], 404));
        }
        $gitHubMilestone->delete();
    }
}