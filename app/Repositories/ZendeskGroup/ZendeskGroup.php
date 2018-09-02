<?php

namespace App\Repositories\ZendeskGroup;

use Illuminate\Database\Eloquent\Model;

class ZendeskGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'name',
        'url',
        'deleted',
    ];

}
