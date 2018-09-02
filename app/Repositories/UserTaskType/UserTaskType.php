<?php

namespace App\Repositories\UserTaskType;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the UserTaskType entity
*/
class UserTaskType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'task_type_id'
    ];
}