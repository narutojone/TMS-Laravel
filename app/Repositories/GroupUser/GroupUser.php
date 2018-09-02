<?php

namespace App\Repositories\GroupUser;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = 'group_user';
    /**
     * @var array
     */
    protected $fillable = [
        'group_id',
        'user_id'
    ];
}
