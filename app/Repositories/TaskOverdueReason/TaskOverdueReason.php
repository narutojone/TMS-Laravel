<?php

namespace App\Repositories\TaskOverdueReason;

use Illuminate\Database\Eloquent\Model;

class TaskOverdueReason extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'active',
        'user_id',
        'reason_id',
        'counter',
        'comment',
        'expired_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expired_at',
    ];

    /**
     * Get the task that owns the overdue reason.
     */
    public function task()
    {
        return $this->belongsTo('App\Repositories\Task\Task', 'task_id');
    }

    /**
     * Get the overdue reason that owns the task overdue reason.
     */
    public function overdueReason()
    {
        return $this->belongsTo('App\Repositories\OverdueReason\OverdueReason', 'reason_id');
    }

	public function reason()
	{
		return $this->belongsTo('App\Repositories\OverdueReason\OverdueReason', 'reason_id');
	}

    /**
     * Get the user that owns the task overdue reason.
     */
    public function user()
    {
        return $this->belongsTo('App\Repositories\User\User');
    }
}
