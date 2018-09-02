<?php

namespace App\Repositories\TasksUserAcceptance;

use Illuminate\Database\Eloquent\Model;

/**
 * The model for the TasksUserAcceptance entity
 */
class TasksUserAcceptance extends Model
{
    protected $table = 'tasks_user_acceptance';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'user_id',
        'template_id',
        'version_no',
    ];
}