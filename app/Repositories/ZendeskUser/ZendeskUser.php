<?php

namespace App\Repositories\ZendeskUser;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the ZendeskUser entity
*/
class ZendeskUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zendesk_id',
        'name',
        'email'
    ];
}