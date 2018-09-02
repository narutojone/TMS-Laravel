<?php
 
namespace App\Repositories\Review;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryReview extends BaseEloquentRepository implements ReviewInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryReview constructor.
     *
     * @param Review $model
     */
    public function __construct(Review $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Review.
     *
     * @param array $input
     *
     * @return Review
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['crate']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a Review.
     *
     * @param integer $id
     * @param array $input
     *
     * @return Review
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $review = $this->find($id);
        if ($review) {
            $review->fill($input);
            $review->save();
            return $review;
        }

        throw new ModelNotFoundException('Model Review not found.', 404);
    }

    /**
     * Delete a Review.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $review = $this->model->find($id);
        if (!$review) {
            throw new ModelNotFoundException('Model Review not found.', 404);
        }
        $review->delete();
    }

    /**
     * Get pending reviews for a reviewer.
     *
     * @param $userId
     * @return Collection|null
     */
    public function getReviewsPending($userId = null) : ?Collection
    {
        // Get all pending reviews
        $pendingReviews = $this->model->where('status', Review::STATUS_PENDING);

        // Filter by user ID
        if ($userId) {
            $pendingReviews = $pendingReviews->where('reviewer_id', $userId);
        }

        // Fetch results
        $pendingReviews = $pendingReviews->get();

        return $pendingReviews;
    }

    /**
     * Check if a review has pending tasks.
     *
     * @param Review $review
     * @return bool
     */
    public function hasPendingTasks(Review $review) : bool
    {
        $hasPendingTasks = false;

        foreach ($review->userCompletedTasks as $completedTaskForReview)
        {
            if ($completedTaskForReview->status == UserCompletedTask::STATUS_PENDING) {
                $hasPendingTasks = true;
            }
        }

        return $hasPendingTasks;
    }

    /**
     * Check if a review has declined tasks.
     *
     * @param Review $review
     * @return bool
     */
    public function hasDeclinedTasks(Review $review) : bool
    {
        $hasDeclinedTasks = false;

        foreach ($review->userCompletedTasks as $completedTaskForReview)
        {
            if ($completedTaskForReview->status == UserCompletedTask::STATUS_DECLINED) {
                $hasDeclinedTasks = true;
            }
        }

        return $hasDeclinedTasks;
    }

    /**
     * Check if a review has only approved tasks.
     *
     * @param Review $review
     * @return bool
     */
    public function hasOnlyApprovedTasks(Review $review) : bool
    {
        $hasOnlyApprovedTasks = true;

        foreach ($review->userCompletedTasks as $completedTaskForReview)
        {
            if ($completedTaskForReview->status != UserCompletedTask::STATUS_APPROVED) {
                $hasOnlyApprovedTasks = false;
            }
        }

        return $hasOnlyApprovedTasks;
    }


    /**
     * Check if the reviewer id is the same with the user reviewed.
     *
     * @param Review $review
     * @return bool
     */
    public function validateReviewerAndUserToReview(Review $review) : bool {
        if ($review->user_id == $review->reviewer_id) {
            return true;
        }

        return false;
    }
}