<?php

namespace App\Repositories\Review;

use App\Repositories\Task\Task;
use App\Repositories\User\User;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_DECLINED = 'declined';
    const STATUS_APPROVED = 'approved';

    const CRITICAL_YES = true;
    const CRITICAL_NO = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_level',
        'reviewer_id',
        'reviewer_task_id',
        'status',
        'reason',
        'critical',
        'completed_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'completed_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function isCritical()
    {
        return $this->critical;
    }


    public function userCompletedTasks()
    {
        return $this->hasMany(UserCompletedTask::class);
    }

    public function declinedUserCompletedTasks()
    {
        return $this->userCompletedTasks()->where('status', '=', UserCompletedTask::STATUS_DECLINED);
    }
    /**
     * Return the user that is reviewed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userReviewed() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Return the reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Returns the reviewer's task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewerTask() : BelongsTo
    {
        return $this->belongsTo(Task::class, 'reviewer_task_id');
    }
}
