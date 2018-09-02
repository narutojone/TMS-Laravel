<?php

namespace App\Repositories\UserSystem;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the UserSystem entity
*/
class UserSystem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'system_id'
    ];
}