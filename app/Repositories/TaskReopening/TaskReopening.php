<?php

namespace App\Repositories\TaskReopening;

use Illuminate\Database\Eloquent\Model;

class TaskReopening extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'reason',
        'completed_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at',
    ];
}
