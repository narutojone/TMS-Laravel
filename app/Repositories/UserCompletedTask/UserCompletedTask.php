<?php

namespace App\Repositories\UserCompletedTask;

use App\Repositories\Review\Review;
use App\Repositories\Task\Task;
use App\Repositories\Template\Template;
use App\Repositories\User\User;
use App\Repositories\UserCompletedSubtask\UserCompletedSubtask;
use Illuminate\Database\Eloquent\Model;

/** 
 * The model for the UserCompletedTask entity
*/
class UserCompletedTask extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_DECLINED = 'declined';
    const STATUS_APPROVED = 'approved';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'user_level',
        'task_id',
        'template_id',
        'review_id',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = [
        'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function userCompletedSubtasks()
    {
        return $this->hasMany(UserCompletedSubtask::class);
    }
}