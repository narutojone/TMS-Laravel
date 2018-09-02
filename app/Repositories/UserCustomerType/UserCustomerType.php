<?php

namespace App\Repositories\UserCustomerType;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the UserCustomerType entity
*/
class UserCustomerType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_type_id'
    ];
}