<?php

namespace App\Repositories\OverdueReason;

use App\Repositories\TaskOverdueReason\TaskOverdueReason;
use Illuminate\Database\Eloquent\Model;

class OverdueReason extends Model
{
    const DIRECTION_DOWN = 'down';
    const DIRECTION_UP = 'up';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'description',
        'required',
        'priority',
        'visible',
        'default',
        'active',
        'hex',
        'is_visible_in_report',
        'threshold_value',
        'days',
        'complete_task',
        'completed_user_id',
    ];

    /**
     * The task overdue reasons owned by the overdue reason.
     */
    public function taskOverdueReasons()
    {
        return $this->hasMany(TaskOverdueReason::class, 'reason_id');
    }
}
