<?php

namespace App\Repositories\UserCompletedSubtask;

use App\Repositories\Subtask\Subtask;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use Illuminate\Database\Eloquent\Model;

/** 
 * The model for the UserCompletedSubtask entity
*/
class UserCompletedSubtask extends Model
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
        'user_completed_task_id',
        'subtask_id',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = [
        'created_at', 'updated_at'
    ];

    public function userCompletedTask()
    {
        return $this->belongsTo(UserCompletedTask::class);
    }

    public function subtask()
    {
        return $this->belongsTo(Subtask::class);
    }
}