<?php

namespace App\Repositories\TaskType;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $table = 'task_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

}
