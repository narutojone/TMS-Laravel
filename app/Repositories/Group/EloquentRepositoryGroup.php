<?php
 
namespace App\Repositories\Group;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\GroupUser\GroupUserInterface;
use App\Repositories\Review\Review;
use App\Repositories\Review\ReviewInterface;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGroup extends BaseEloquentRepository implements GroupInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGroup constructor.
     *
     * @param \App\Repositories\Group\Group $model
     */
    public function __construct(Group $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Group.
     *
     * @param array $input
     * @return \App\Repositories\Group\Group
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Update a Group.
     *
     * @param integer $id
     * @param array $input
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $group = $this->find($id);
        if ($group) {
            $group->fill($input);
            $group->save();
            return $group;
        }

        throw new ModelNotFoundException('Model GroupTemplate not found.', 404);
    }

    /**
     * Delete a Group.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $group = $this->model->find($id);
        if (!$group) {
            throw new ModelNotFoundException('Model GroupTemplate not found.', 404);
        }
        $group->delete();
    }

    /**
     * Get user for review, check what review is needed, first or second and consider if we are not at the first review if the first review was decined with critical or not
     *
     * @param int $userId
     * @param int $userLevel
     * @return int|null - id of the user that is going to make the review
     */
    public function getUserForReview(int $userId, int $userLevel = 1) : ?int
    {
        // First check if user is at first review or second review.
        // We do this because the second reviewer has to be different than the first one
        $reviewsRepository = app()->make(ReviewInterface::class);
        $reviews = $reviewsRepository->model()
            ->where([
                'user_id'       => $userId,
                'user_level'    => $userLevel,
            ])->get();

        // It means that the user had no review for the current level so we need to assign a user from the first reviewer group.
        if (!$reviews->toArray()) {
            return $this->getUserFromFirstGroupreviewers();
        }

        $previousReviewers = $reviews->pluck('reviewer_id')->toArray();
        // Maybe we will support more than two reviews in the future for a bigger user level.

        // Check if we have a review already done and if the last review done was closed with critical
        if (count($reviews->toArray()) > 0 && (last($reviews->toArray())['critical'] == Review::CRITICAL_YES)) {
            return $this->getUserFromSecondGroupReviewers($previousReviewers);
        } elseif (count($reviews->toArray() > 0)) { // if we have at least one more review before for the current user and the last one done was not declined with critical
            return $this->getUserFromFirstGroupreviewers();
        }

        return null;
    }

    /**
     * Get user for first review.
     *
     * @return int|null - id of the user for first review
     */
    public function getUserFromFirstGroupreviewers() : ?int
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $firstReviewerGroupId = $reviewSettingsRepository->model()->first()->first_review_group_id;

        // This should not be happening.
        if (!$firstReviewerGroupId) {
            return null;
        }

        // Get users assigned to the first review group.
        $groupUserRepository = app()->make(GroupUserInterface::class);
        $firstGroupUsersCollection = $groupUserRepository->model()->where('group_id', $firstReviewerGroupId)->get();
        $firstGroupUserIds = $firstGroupUsersCollection->pluck('user_id')->toArray();

        // This should not pe happening.
        if (empty($firstGroupUserIds)) {
            return null;
        }

        $key = array_rand($firstGroupUserIds);
        return $firstGroupUserIds[$key];
    }

    /**
     * Get user for second review.
     *
     * @param array $previousReviewers
     * @return int|null - id of the user for second review
     */
    public function getUserFromSecondGroupReviewers(array $previousReviewers) : ?int
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $secondReviewerGroupId = $reviewSettingsRepository->model()->first()->second_review_group_id;

        // This should not be happening.
        if (!$secondReviewerGroupId) {
            return null;
        }

        // Get users assigned to the second review group.
        $groupUserRepository = app()->make(GroupUserInterface::class);
        $secondGroupUsersCollection = $groupUserRepository->model()
            ->where('group_id', $secondReviewerGroupId)
            ->whereNotIn('user_id', $previousReviewers)
            ->get();
        $secondGroupUserIds = $secondGroupUsersCollection->pluck('user_id')->toArray();

        // This should not pe happening.
        if (empty($secondGroupUserIds)) {
            return null;
        }

        $key = array_rand($secondGroupUserIds);
        return $secondGroupUserIds[$key];
    }
}