<?php

namespace App\Repositories\TaskDetails;

use App\Repositories\Task\Task;
use Illuminate\Database\Eloquent\Model;

class TaskDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'description',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'id', 'task_id');
    }
}
